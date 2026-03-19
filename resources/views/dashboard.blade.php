@extends('layouts.app')

@section('title', 'ইউজার ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">স্বাগতম, {{ auth()->user()->name }}!</h1>
        <p class="text-slate-500 font-medium mt-1">আপনার রক্তদান এবং রিকোয়েস্টের বিস্তারিত ড্যাশবোর্ড।</p>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-slate-200 mb-8 shadow-lg shadow-slate-100/50">
        <div class="p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4 flex items-center gap-2">
                <span class="p-2 bg-red-100 rounded-lg text-red-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path></svg>
                </span>
                মেডিকেল এলিজিবিলিটি স্ট্যাটাস
            </h3>

            @if(auth()->user()->canDonate())
                <div class="flex items-start p-5 text-emerald-800 border border-emerald-200 rounded-2xl bg-emerald-50/50">
                    <svg class="flex-shrink-0 w-8 h-8 mr-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <h4 class="font-bold text-xl">আপনি রক্তদানের জন্য প্রস্তুত!</h4>
                        <p class="text-sm mt-1 text-emerald-700 font-medium">আপনার কোনো কুলডাউন পিরিয়ড নেই। আপনি চাইলে এখনই মুমূর্ষু রোগীর রক্তের রিকোয়েস্ট এক্সেপ্ট করতে পারেন।</p>
                    </div>
                </div>
            @else
                <div class="p-5 text-amber-900 border border-amber-200 rounded-2xl bg-amber-50/50">
                    <div class="flex items-start mb-6">
                        <svg class="flex-shrink-0 w-8 h-8 mr-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h4 class="font-bold text-xl text-amber-800">কুলডাউন পিরিয়ড চলছে</h4>
                            <p class="text-sm mt-1 font-medium">
                                মেডিকেল গাইডলাইন অনুযায়ী আপনি আগামী <strong>{{ auth()->user()->daysUntilNextDonation() }} দিন</strong> রক্ত দিতে পারবেন না। আপনার পরবর্তী রক্তদানের সম্ভাব্য তারিখ: <span class="px-2 py-0.5 bg-white border border-amber-200 rounded-md font-extrabold text-slate-800">{{ auth()->user()->next_eligible_date->format('d M, Y') }}</span>
                            </p>
                        </div>
                    </div>
                    
                    @php
                        $totalDays = 90;
                        $daysLeft = auth()->user()->daysUntilNextDonation();
                        $daysPassed = $totalDays - $daysLeft;
                        $progressPercentage = ($daysPassed / $totalDays) * 100;
                    @endphp
                    
                    <div class="w-full bg-amber-200/50 rounded-full h-4 overflow-hidden border border-amber-200">
                      <div class="bg-amber-500 h-4 rounded-full transition-all duration-1000 ease-out shadow-inner" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-amber-700 font-black mt-3 px-1 uppercase tracking-tighter">
                        <span>০ দিন (শেষ দান)</span>
                        <span class="bg-amber-200 px-3 py-1 rounded-full">{{ $daysPassed }} দিন পার হয়েছে</span>
                        <span>৯০ দিন (যোগ্যতা)</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mb-10 bg-slate-50 p-6 rounded-3xl border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide">তথ্য আপডেট</h3>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">রক্তদানের সঠিক তথ্য আমাদের সিস্টেমকে আরও কার্যকর করে।</p>
            </div>
        </div>

        <form action="{{ route('donation.record.update') }}" method="POST" class="flex items-end gap-3 w-full md:w-auto">
            @csrf
            <div class="flex-1 md:w-48">
                <input type="date" name="last_donated_at" value="{{ auth()->user()->last_donated_at?->format('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-red-500 focus:ring-red-500 font-bold text-slate-700">
            </div>
            <button type="submit" class="bg-slate-900 hover:bg-black text-white px-6 py-2.5 rounded-xl font-extrabold transition-all active:scale-95 shadow-lg shadow-slate-200">
                সেভ করুন
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-slate-500 text-xs font-black uppercase tracking-widest">মোট রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-slate-900">{{ $totalRequestsMade ?? 0 }}</span>
                <span class="text-slate-400 font-bold text-sm">টি</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-emerald-200 transition-colors">
            <div class="text-emerald-600 text-xs font-black uppercase tracking-widest">আপনার অবদান</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $totalContributions ?? 0 }}</span>
                <span class="text-emerald-400 font-bold text-sm">বার</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-red-600 text-xs font-black uppercase tracking-widest">সফল রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-red-600">{{ $fulfilledRequests ?? 0 }}</span>
                <span class="text-red-400 font-bold text-sm">টি</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-blue-600 text-xs font-black uppercase tracking-widest">সফলতার হার</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-blue-600">{{ $successRate ?? 0 }}</span>
                <span class="text-blue-400 font-bold text-sm">%</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <a href="{{ route('requests.create') }}" class="group p-8 rounded-3xl bg-red-600 hover:bg-red-700 transition shadow-xl shadow-red-200">
            <div class="text-white font-black text-2xl mb-2">জরুরি রক্তের দরকার?</div>
            <p class="text-red-100 text-sm font-bold opacity-90">সহজেই নতুন রিকোয়েস্ট তৈরি করুন এবং ডোনারদের সাথে যোগাযোগ করুন।</p>
        </a>
        <a href="{{ route('requests.index') }}" class="group p-8 rounded-3xl bg-white border-2 border-slate-200 hover:border-red-500 transition shadow-sm">
            <div class="text-slate-900 font-black text-2xl mb-2">রক্ত দিতে চান?</div>
            <p class="text-slate-500 text-sm font-bold">আপনার এরিয়ার সাম্প্রতিক রিকোয়েস্টগুলো দেখুন এবং সাড়া দিন।</p>
        </a>
    </div>

    @if(isset($recentRequests))
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-12">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">সর্বশেষ ৫টি রিকোয়েস্টের আপডেট</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest font-black">
                        <th class="px-6 py-4 border-b border-slate-100">রোগীর নাম ও গ্রুপ</th>
                        <th class="px-6 py-4 border-b border-slate-100">দরকার</th>
                        <th class="px-6 py-4 border-b border-slate-100">সাড়া (Accepted)</th>
                        <th class="px-6 py-4 border-b border-slate-100">স্ট্যাটাস</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($recentRequests as $req)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900">{{ $req->patient_name ?? 'রোগী' }}</div>
                                <div class="text-xs font-black text-red-600 mt-0.5">{{ $req->blood_group?->value ?? (string) $req->blood_group }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $req->needed_at?->format('d M, Y') ?? 'ASAP' }}</div>
                                <div class="text-[10px] text-slate-400 font-black uppercase">{{ $req->needed_at?->format('h:i A') ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-emerald-600">{{ $req->accepted_responses ?? 0 }}</span>
                                    <span class="text-slate-300 font-bold">/</span>
                                    <span class="font-bold text-slate-500">{{ $req->total_responses ?? 0 }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(strtolower($req->status) === 'fulfilled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 uppercase">Fulfilled</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-700 uppercase">{{ $req->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-700 hover:bg-slate-900 hover:text-white transition-all shadow-sm">ডিটেইলস</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-bold">আপনি এখনো কোনো রক্তের রিকোয়েস্ট করেননি।</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection