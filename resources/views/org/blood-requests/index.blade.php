@extends('layouts.app')

@section('title', 'রক্তের অনুরোধ (অর্গ জোন) — রক্তদূত')

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

    {{-- 🧭 Top Navigation Tabs --}}
    <div class="mb-8 flex overflow-x-auto bg-white border border-slate-200 rounded-2xl p-2 shadow-sm gap-2 whitespace-nowrap">
        <a href="{{ route('org.dashboard') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            👥 মেম্বার ম্যানেজমেন্ট
        </a>
        <a href="{{ route('org.requests.index') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all bg-red-600 text-white shadow-sm">
            🩸 রক্তের অনুরোধ (অর্গ জোন)
        </a>
        <a href="{{ route('org.camps.index') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-teal-600">
            🏕️ রক্তদান ক্যাম্প
        </a>
    </div>

    {{-- 🚨 Blood Requests Feed --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 mb-8">
        <div class="mb-6 border-b border-slate-100 pb-4">
            <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="text-red-500">📍</span> আপনার এরিয়ার জরুরি অনুরোধ
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                @if($org->upazila && $org->locationUpazila)
                    {{ $org->locationUpazila->bn_name }} উপজেলার সকল বর্তমান পেন্ডিং ব্লাড রিকোয়েস্ট।
                @elseif($org->district && $org->locationDistrict)
                    {{ $org->locationDistrict->bn_name }} জেলার সকল বর্তমান পেন্ডিং ব্লাড রিকোয়েস্ট।
                @endif
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($requests as $request)
                <div class="border border-slate-200 rounded-2xl p-5 hover:border-red-100 hover:shadow-lg hover:shadow-red-500/5 transition-all flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-lg font-black text-sm">
                                {{ $request->blood_group?->value ?? (string) $request->blood_group }}
                            </span>
                            <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $request->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-black text-slate-900 mb-1">{{ $request->patient_name }}</h3>
                        <p class="text-sm text-slate-600 font-medium mb-4 flex items-start gap-1.5">
                            <svg class="w-4 h-4 mt-0.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span>{{ $request->hospital ?? 'হাসপাতাল উল্লেখ নেই' }} <br><span class="text-xs text-slate-400">{{ $request->upazila?->bn_name ?? $request->district?->bn_name ?? '' }}</span></span>
                        </p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                        @php
                            $isBroadcasted = \App\Models\BroadcastLog::where('organization_id', $org->id)->where('blood_request_id', $request->id)->exists();
                            $shareText = "জরুরি রক্তের প্রয়োজন\nরোগী: {$request->patient_name}\nরক্তের গ্রুপ: " . ($request->blood_group?->value ?? (string) $request->blood_group) . "\nহাসপাতাল: {$request->hospital}\n\nবিস্তারিত দেখতে ক্লিক করুন: " . route('requests.show', $request->id);
                        @endphp

                        @if($isBroadcasted)
                            <button disabled class="flex-1 bg-emerald-50 text-emerald-600 font-extrabold text-xs py-2 rounded-xl text-center border border-emerald-100 flex justify-center items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> ব্রডকাস্ট করা হয়েছে
                            </button>
                        @else
                            <form action="{{ route('org.requests.broadcast', $request->id) }}" method="POST" class="flex-1" onsubmit="return confirm('আপনি কি নিশ্চিত যে আপনার অর্গানাইজেশনের সব মেম্বারকে এই রিকোয়েস্টের নোটিফিকেশন পাঠাতে চান?');">
                                @csrf
                                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs py-2 rounded-xl transition-colors flex justify-center items-center gap-1 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg> ব্রডকাস্ট করুন
                                </button>
                            </form>
                        @endif
                        
                        <button onclick="navigator.clipboard.writeText(`{{ $shareText }}`); alert('বার্তা কপি করা হয়েছে!');" title="শেয়ার বার্তা কপি করুন" class="p-2 bg-slate-50 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition border border-slate-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                    <span class="text-4xl mb-3 block">🌟</span>
                    <h3 class="text-lg font-black text-slate-800">কোনো জরুরি অনুরোধ নেই</h3>
                    <p class="text-sm font-medium text-slate-500 mt-1">আপনার এরিয়াতে বর্তমানে কোনো পেন্ডিং ব্লাড রিকোয়েস্ট নেই।</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
