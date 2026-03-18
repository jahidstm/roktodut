@extends('layouts.app')

@section('title', 'ইউজার ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">স্বাগতম, {{ auth()->user()->name }}!</h1>
        <p class="text-slate-500 font-medium">আপনার রক্তদান এবং রিকোয়েস্টের সংক্ষিপ্ত সারসংক্ষেপ।</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-slate-900">{{ $totalRequestsMade }}</span>
                <span class="text-slate-400 font-bold text-sm">টি</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">আপনার অবদান</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $totalContributions }}</span>
                <span class="text-emerald-400 font-bold text-sm">বার</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-red-600">{{ $fulfilledRequests }}</span>
                <span class="text-red-400 font-bold text-sm">টি</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">সফলতার হার</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-blue-600">{{ $successRate }}</span>
                <span class="text-blue-400 font-bold text-sm">%</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <a href="{{ route('requests.create') }}" class="group p-8 rounded-3xl bg-red-600 hover:bg-red-700 transition shadow-lg shadow-red-200">
            <div class="text-white font-black text-xl mb-2">জরুরি রক্তের দরকার?</div>
            <p class="text-red-100 text-sm font-medium">সহজেই নতুন রিকোয়েস্ট তৈরি করুন এবং ডোনারদের সাথে যোগাযোগ করুন।</p>
        </a>

        <a href="{{ route('requests.index') }}" class="group p-8 rounded-3xl bg-white border-2 border-slate-200 hover:border-red-500 transition shadow-sm">
            <div class="text-slate-900 font-black text-xl mb-2">রক্ত দিতে চান?</div>
            <p class="text-slate-500 text-sm font-medium">আপনার এরিয়ার সাম্প্রতিক রিকোয়েস্টগুলো দেখুন এবং সাড়া দিন।</p>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">আপনার সাম্প্রতিক রিকোয়েস্টসমূহ</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">সর্বশেষ ৫টি রিকোয়েস্টের আপডেট</p>
            </div>
            <a href="{{ route('requests.index', ['my_requests' => 1]) }}" class="text-sm font-bold text-red-600 hover:text-red-700 transition">সব দেখুন →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-bold">
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
                                <div class="text-xs font-bold text-red-600 mt-0.5">{{ $req->blood_group?->value ?? (string) $req->blood_group }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-700">{{ $req->needed_at?->format('d M, Y') ?? 'ASAP' }}</div>
                                <div class="text-xs text-slate-500 font-medium">{{ $req->needed_at?->format('h:i A') ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-emerald-600">{{ $req->accepted_responses }}</span>
                                    <span class="text-slate-400">/</span>
                                    <span class="font-semibold text-slate-500">{{ $req->total_responses }} জন</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if(strtolower($req->status) === 'fulfilled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">
                                        Fulfilled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 uppercase">
                                        {{ $req->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('requests.show', $req->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-extrabold text-slate-700 hover:bg-slate-100 hover:text-red-600 transition">
                                    ডিটেইলস
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                <div class="text-slate-500 font-medium mb-2">আপনি এখনো কোনো রক্তের রিকোয়েস্ট করেননি।</div>
                                <a href="{{ route('requests.create') }}" class="text-red-600 font-bold hover:underline">প্রথম রিকোয়েস্ট তৈরি করুন</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection