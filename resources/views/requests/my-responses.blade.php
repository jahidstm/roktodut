@php
    $layout = 'layouts.app';
    if(auth()->check()){
        $u = auth()->user();
        if($u->isAdmin()) {
            $layout = 'layouts.admin-dashboard';
        } elseif($u->isOrgAdmin()) {
            $layout = 'layouts.org-dashboard';
        } elseif($u->isDonor()) {
            $layout = 'layouts.donor-dashboard';
        } else {
            $layout = 'layouts.user-dashboard';
        }
    }
@endphp
@extends($layout)

@section('title', 'রিকোয়েস্টে সাড়া — রক্তদূত')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6" data-panel-id="my-responses">

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden scroll-reveal" data-scroll-reveal>
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900">আপনার রিকোয়েস্টে যারা যারা সাড়া দিয়েছেন</h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">আপনার তৈরি করা রিকোয়েস্টে ডোনারদের রেসপন্স</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider font-bold text-slate-500">
                        <th class="px-6 py-4">ডোনারের নাম ও গ্রুপ</th>
                        <th class="px-6 py-4">রোগী (যার জন্য দরকার)</th>
                        <th class="px-6 py-4">সাড়ার সময়</th>
                        <th class="px-6 py-4">স্ট্যাটাস</th>
                        <th class="px-6 py-4 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($responses as $res)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-slate-900">{{ $res->user?->name ?? 'অজ্ঞাত ডোনার' }}</div>
                            <div class="text-xs font-bold text-red-600 mt-0.5">{{ $res->user?->blood_group?->value ?? $res->user?->blood_group ?? 'অজ্ঞাত গ্রুপ' }} ডোনার</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $res->bloodRequest?->patient_name ?? 'তথ্য নেই' }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">দরকার: {{ $res->bloodRequest?->needed_at ? $res->bloodRequest->needed_at->format('d M, Y h:i A') : 'ASAP' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-700">{{ $res->created_at->format('d M, Y') }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $res->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($res->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">Pending</span>
                            @elseif($res->status === 'accepted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">Accepted</span>
                            @elseif($res->status === 'declined' || $res->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-red-100 text-red-800">Declined</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-100 text-slate-800">{{ ucfirst($res->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('requests.show', $res->blood_request_id) }}" class="inline-flex items-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 transition">ডিটেইলস</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <p class="font-bold text-slate-600">আপনার রিকোয়েস্টে এখনও কেউ সাড়া দেয়নি।</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($responses->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $responses->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
