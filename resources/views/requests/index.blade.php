@extends('layouts.app')

@section('title', 'রিকোয়েস্ট ফিড — রক্তদূত')

@section('content')
{{-- 🎯 THE FIX: এই মেইন কন্টেইনার ডিভটি পুরো কন্টেন্টকে র‍্যাপ করে মাঝখানে রাখবে --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-start justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight">রক্তের রিকোয়েস্ট ফিড</h1>
            <p class="text-slate-500 font-medium mt-1">সাম্প্রতিক পেন্ডিং রিকোয়েস্টগুলো</p>
        </div>

        <a href="{{ route('requests.create') }}"
           class="shrink-0 inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-extrabold shadow-sm shadow-red-200">
            নতুন রিকোয়েস্ট
        </a>
    </div>

    {{-- 🎯 Advanced Filter Section (Server-side Divisions + AJAX Cascade) --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8">
        <form action="{{ route('requests.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            
            <div>
                <label for="blood_group" class="block text-sm font-bold text-slate-700 mb-1">রক্তের গ্রুপ</label>
                <select name="blood_group" id="blood_group" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                    <option value="">সব গ্রুপ</option>
                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                        <option value="{{ $bg }}" {{ request('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter_division" class="block text-sm font-bold text-slate-700 mb-1">বিভাগ</label>
                <select name="division_id" id="filter_division" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                    <option value="">বিভাগ নির্বাচন</option>
                    {{-- 🚀 Server-side fetching (Fast & Reliable) --}}
                    @foreach(\App\Models\Division::orderBy('name', 'asc')->get() as $div)
                        <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter_district" class="block text-sm font-bold text-slate-700 mb-1">জেলা</label>
                <select name="district_id" id="filter_district" disabled class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                    <option value="">প্রথমে বিভাগ নির্বাচন করুন</option>
                </select>
            </div>

            <div>
                <label for="filter_upazila" class="block text-sm font-bold text-slate-700 mb-1">উপজেলা/থানা</label>
                <select name="upazila_id" id="filter_upazila" disabled class="w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-700">
                    <option value="">প্রথমে জেলা নির্বাচন করুন</option>
                </select>
            </div>

             <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                 @if(request()->hasAny(['blood_group', 'division_id', 'district_id', 'upazila_id']))
                     <a href="{{ route('requests.index') }}" class="shrink-0 bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-2.5 rounded-lg font-extrabold transition-colors flex items-center justify-center">
                         ক্লিয়ার
                    </a>
                @endif
                 <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-8 py-2.5 rounded-lg font-extrabold shadow-sm transition-colors">
                     খুঁজুন
                 </button>
             </div>

            <input type="hidden" id="selectedDivision" value="{{ request('division_id', '') }}">
            <input type="hidden" id="selectedDistrict" value="{{ request('district_id', '') }}">
            <input type="hidden" id="selectedUpazila" value="{{ request('upazila_id', '') }}">
         </form>
     </div>

    @if ($requests->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center">
            <div class="text-slate-900 font-extrabold text-lg">কোনো পেন্ডিং রিকোয়েস্ট পাওয়া যায়নি</div>
            <div class="text-slate-500 text-sm mt-2 font-medium">নতুন রিকোয়েস্ট তৈরি হলে এখানে দেখাবে। অথবা আপনার ফিল্টার পরিবর্তন করে দেখতে পারেন।</div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ($requests as $r)
                <x-request-feed-card :request="$r" :show-requester="true" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $requests->links() }}
        </div>
    @endif

{{-- 🎯 THE FIX: এই ডিভটি কন্টেইনার ক্লোজ করছে --}}
</div>
@endsection
