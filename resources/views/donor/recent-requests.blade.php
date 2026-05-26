@extends('layouts.donor-dashboard')

@section('title', 'সাম্প্রতিক রিকোয়েস্ট — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6" data-panel-id="recent-requests">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">আপনি যেসব রিকোয়েস্টে সাড়া দিয়েছেন (গৃহীত)</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider font-bold text-slate-500">
                        <th class="px-6 py-4">রিকোয়েস্ট ও গ্রুপ</th>
                        <th class="px-6 py-4">দরকার</th>
                        <th class="px-6 py-4">সাড়া (গৃহীত)</th>
                        <th class="px-6 py-4">স্ট্যাটাস</th>
                        <th class="px-6 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentRequests as $req)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $req->patient_name ?? 'তথ্য নেই' }}</div>
                            <div class="text-xs font-bold text-red-600 mt-0.5">{{ $req->blood_group?->value ?? $req->blood_group }} ডোনার দরকার</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $req->needed_at ? $req->needed_at->format('d M, Y h:i A') : 'ASAP' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $req->accepted_responses ?? 0 }} জন</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($req->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">Pending</span>
                            @elseif($req->status === 'fulfilled')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">Fulfilled</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-800">{{ ucfirst($req->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">ডিটেইলস</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <p class="font-bold text-slate-600">আপনি এখনো কোনো রিকোয়েস্টে গৃহীত সাড়া দেননি।</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
