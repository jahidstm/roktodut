@extends('layouts.app')

@section('title', 'ক্যাম্প ম্যানেজমেন্ট - রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('org.camps.index') }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition text-slate-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $camp->name }}</h1>
                <p class="text-slate-500 font-medium">তারিখ: {{ $camp->camp_date->format('d M, Y') }} | স্থান: {{ $camp->location }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- 🏃‍♂️ Log Attendance Form --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sticky top-8">
                <h3 class="text-lg font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-teal-500">📝</span> উপস্থিতি লগ করুন
                </h3>
                <p class="text-sm font-medium text-slate-500 mb-6">ক্যাম্পে উপস্থিত ভেরিফাইড ডোনারদের নির্বাচন করে তাদের উপস্থিতি নিশ্চিত করুন। উপস্থিতির জন্য ডোনার ১০০ পয়েন্ট পাবেন।</p>
                
                <form action="{{ route('org.camps.attendance', $camp->id) }}" method="POST">
                    @csrf
                    <div class="mb-5">
                        <label for="user_id" class="block text-sm font-extrabold text-slate-900 mb-2">ডোনার নির্বাচন করুন <span class="text-red-500">*</span></label>
                        <select name="user_id" id="user_id" required class="w-full rounded-xl border-slate-200 focus:border-teal-500 focus:ring-teal-500 bg-slate-50 text-slate-900 px-4 py-3 font-bold">
                            <option value="">-- ডোনার বেছে নিন --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->blood_group?->value ?? (string) $member->blood_group }}) - {{ $member->phone }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1.5 font-medium">* শুধুমাত্র ক্লাবের ভেরিফাইড মেম্বারদের দেখাচ্ছে।</p>
                    </div>

                    <button type="submit" class="w-full px-6 py-3.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-sm font-black shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        উপস্থিতি নিশ্চিত করুন
                    </button>
                </form>
            </div>
        </div>

        {{-- 📋 Attendance List --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">লগড ডোনারগণ</h2>
                        <p class="text-xs text-slate-500 font-bold mt-1 uppercase tracking-tight">সর্বমোট: {{ $camp->attendances->count() }} জন</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-black">
                                <th class="px-6 py-4 border-b border-slate-100">ডোনারের নাম</th>
                                <th class="px-6 py-4 border-b border-slate-100">যোগাযোগ</th>
                                <th class="px-6 py-4 border-b border-slate-100">লগ করার সময়</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($camp->attendances as $attendance)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-extrabold text-slate-900">{{ $attendance->user->name }}</div>
                                        <div class="text-xs font-black text-red-600 mt-0.5">{{ $attendance->user->blood_group?->value ?? (string) $attendance->user->blood_group }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-700">{{ $attendance->user->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 font-medium text-xs">
                                        {{ $attendance->created_at->format('d M, Y h:ia') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center bg-slate-50">
                                        <h3 class="text-slate-800 font-extrabold">এখনো কোনো ডোনার লগ করা হয়নি</h3>
                                        <p class="text-slate-500 text-sm mt-1">ক্যাম্পে উপস্থিত ডোনারদের বাম পাশের ফর্ম থেকে নির্বাচন করে লগ করুন।</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
