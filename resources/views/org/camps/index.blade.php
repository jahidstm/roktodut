@extends('layouts.app')

@section('title', 'রক্তদান ক্যাম্প - রক্তদূত')

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
        <a href="{{ route('org.dashboard') }}" data-org-tab class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            👥 মেম্বার ম্যানেজমেন্ট
        </a>
        <a href="{{ route('org.requests.index') }}" data-org-tab class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-red-600">
            🩸 রক্তের অনুরোধ (অর্গ জোন)
        </a>
        <a href="{{ route('org.camps.index') }}" data-org-tab class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all bg-teal-600 text-white shadow-sm">
            🏕️ রক্তদান ক্যাম্প
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">🏕️ অর্গানাইজড ব্লাড ক্যাম্প</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">ক্যাম্প পরিকল্পনা, প্রকাশ ও উপস্থিতি ট্র্যাকিং</p>
            </div>

            <a href="{{ route('org.camps.create') }}" class="inline-flex justify-center items-center gap-2 bg-slate-900 text-white font-extrabold text-sm px-6 py-2.5 rounded-xl hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                নতুন ক্যাম্প তৈরি করুন
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-black">
                        <th class="px-6 py-4 border-b border-slate-100">ক্যাম্পের নাম</th>
                        <th class="px-6 py-4 border-b border-slate-100">তারিখ ও সময়</th>
                        <th class="px-6 py-4 border-b border-slate-100">লোকেশন</th>
                        <th class="px-6 py-4 border-b border-slate-100">স্ট্যাটাস</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-center">উপস্থিতি (রেজিস্টার্ড/চেক-ইন)</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($camps as $camp)
                        @php
                            $status = $camp->effective_status;
                            $statusMap = [
                                'draft' => 'bg-slate-100 text-slate-700',
                                'published' => 'bg-blue-100 text-blue-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $statusLabelMap = [
                                'draft' => 'ড্রাফট',
                                'published' => 'পাবলিশড',
                                'completed' => 'সম্পন্ন',
                                'cancelled' => 'বাতিল',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">{{ $camp->name }}</div>
                                <div class="text-xs text-slate-500 mt-1">যোগাযোগ: {{ $camp->contact_name ?? 'N/A' }} • {{ $camp->contact_phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-teal-700">{{ optional($camp->start_at)->format('d M, Y h:i A') }}</div>
                                <div class="text-xs text-slate-500 mt-1">থেকে {{ optional($camp->end_at)->format('d M, Y h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">
                                    {{ $camp->district?->bn_name ?? $camp->district?->name ?? 'জেলা নেই' }}
                                    /
                                    {{ $camp->upazila?->name ?? 'উপজেলা নেই' }}
                                </div>
                                <div class="text-xs text-slate-500 mt-1">{{ $camp->address_line ?? $camp->location }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black {{ $statusMap[$status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $statusLabelMap[$status] ?? 'ড্রাফট' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-slate-800">{{ $camp->registered_count }}</span>
                                <span class="text-slate-400">/</span>
                                <span class="font-black text-emerald-700">{{ $camp->checked_in_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('org.camps.show', $camp->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg font-extrabold text-xs transition">দেখুন</a>
                                    <a href="{{ route('org.camps.edit', $camp->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg font-extrabold text-xs transition">সম্পাদনা</a>

                                    @if($status === 'draft')
                                        <form action="{{ route('org.camps.publish', $camp->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg font-extrabold text-xs transition hover:bg-blue-700">পাবলিশ</button>
                                        </form>
                                    @endif

                                    @if(in_array($status, ['draft', 'published'], true))
                                        <form action="{{ route('org.camps.cancel', $camp->id) }}" method="POST" onsubmit="return confirm('আপনি কি এই ক্যাম্প বাতিল করতে চান?');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-lg font-extrabold text-xs transition hover:bg-red-700">বাতিল</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center bg-slate-50">
                                <span class="text-3xl block mb-2">🏕️</span>
                                <h3 class="text-slate-800 font-extrabold">এখনও কোনো ক্যাম্প তৈরি করা হয়নি</h3>
                                <p class="text-slate-500 text-sm mt-1">নতুন ক্যাম্প তৈরি করে আপনার অর্গানাইজেশনের রক্তদান কার্যক্রম চালু করুন।</p>
                                <a href="{{ route('org.camps.create') }}" class="mt-4 inline-flex items-center gap-2 bg-slate-900 text-white px-5 py-2.5 rounded-xl text-sm font-extrabold hover:bg-slate-800 transition">
                                    নতুন ক্যাম্প তৈরি করুন
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $camps->links() }}
        </div>
    </div>
    </div>
</div>
@endsection
