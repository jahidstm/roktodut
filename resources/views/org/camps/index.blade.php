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

    {{-- 🧭 Top Navigation Tabs --}}
    <div class="mb-8 flex overflow-x-auto bg-white border border-slate-200 rounded-2xl p-2 shadow-sm gap-2 whitespace-nowrap">
        <a href="{{ route('org.dashboard') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-slate-900">
            👥 মেম্বার ম্যানেজমেন্ট
        </a>
        <a href="{{ route('org.requests.index') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all text-slate-600 hover:bg-slate-50 hover:text-red-600">
            🩸 রক্তের অনুরোধ (অর্গ জোন)
        </a>
        <a href="{{ route('org.camps.index') }}" class="px-5 py-2.5 rounded-xl font-extrabold text-sm transition-all bg-teal-600 text-white shadow-sm">
            🏕️ রক্তদান ক্যাম্প
        </a>
    </div>

    {{-- Camps Header --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">🏕️ অর্গানাইজড ব্লাড ক্যাম্প</h2>
                <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">আপনার ক্লাবের আয়োজিত ক্যাম্পসমূহ</p>
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
                        <th class="px-6 py-4 border-b border-slate-100">তারিখ ও স্থান</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-center">উপস্থিতি (লগড)</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($camps as $camp)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-extrabold text-slate-900 border-l-4 border-transparent hover:border-teal-500">
                                {{ $camp->name }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-teal-700">{{ $camp->camp_date->format('d M, Y') }}</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">{{ Str::limit($camp->location, 30) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-teal-50 text-teal-700 font-black px-3 py-1 rounded-full text-xs border border-teal-100">
                                    {{ $camp->attendances()->count() }} জন
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('org.camps.show', $camp->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:text-teal-700 rounded-lg font-extrabold text-xs transition shadow-sm">
                                    বিস্তারত ও লগ
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center bg-slate-50">
                                <span class="text-3xl block mb-2">🏕️</span>
                                <h3 class="text-slate-800 font-extrabold">কোনো ক্যাম্প আয়োজন করা হয়নি</h3>
                                <p class="text-slate-500 text-sm mt-1">নতুন রক্তদান ক্যাম্প তৈরি করে ডোনারদের ডেটাবেসে লগ করতে পারেন।</p>
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
@endsection
