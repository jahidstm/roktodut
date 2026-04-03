@extends('layouts.app')

@section('title', 'স্মার্ট ডোনার সার্চ — রক্তদূত')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <h2 class="font-bold text-2xl text-gray-800 leading-tight border-l-4 border-red-600 pl-3 mb-6">
            স্মার্ট ডোনার সার্চ
        </h2>

        {{-- 🚨 Alerts (Rate Limit বা ভুল উত্তরের এরর দেখানোর জন্য) --}}
        @if (session('error'))
            <div class="mb-6 rounded-lg bg-red-50 p-4 text-red-700 border-l-4 border-red-500 shadow-sm flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 🔍 Search Form --}}
        <div class="bg-white rounded-xl shadow-md mb-8 overflow-hidden border border-gray-100">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-700">ফিল্টার করুন</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('search') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-5 items-end">
                        {{-- রক্তের গ্রুপ --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                            <select name="blood_group" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200" required>
                                <option value="">সিলেক্ট করুন</option>
                                @foreach ($bloodGroups as $bg)
                                    <option value="{{ $bg->value }}" @selected(($request['blood_group'] ?? '') === $bg->value)>
                                        {{ $bg->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- বিভাগ --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">বিভাগ</label>
                            <select name="division_id" id="division" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                                <option value="">সিলেক্ট করুন</option>
                            </select>
                        </div>

                        {{-- জেলা --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">জেলা</label>
                            <select name="district_id" id="district" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                                <option value="">সিলেক্ট করুন</option>
                            </select>
                        </div>

                        {{-- উপজেলা --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">উপজেলা/এরিয়া</label>
                            <select name="upazila_id" id="upazila" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                                <option value="">সব এলাকা</option>
                            </select>
                        </div>

                        {{-- সাবমিট বাটন --}}
                        <div>
                            <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 bg-red-600 text-white rounded-md font-bold text-sm shadow-md hover:bg-red-700 hover:shadow-lg transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                খুঁজুন
                            </button>
                        </div>
                    </div>

                    {{-- Hidden inputs for JS Location Loaders --}}
                    <input type="hidden" id="selectedDivision" value="{{ $request['division_id'] ?? '' }}">
                    <input type="hidden" id="selectedDistrict" value="{{ $request['district_id'] ?? '' }}">
                    <input type="hidden" id="selectedUpazila" value="{{ $request['upazila_id'] ?? '' }}">
                </form>
            </div>
        </div>

        {{-- 🩸 Search Results --}}
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">সার্চ ফলাফল</h3>
            @if(isset($donors) && $donors->count() > 0)
                <span class="bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded-full">মোট: {{ $donors->total() }} জন</span>
            @endif
        </div>

        @if (!isset($donors) || $donors->isEmpty())
            {{-- No Data State --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 flex flex-col items-center justify-center text-center">
                <div class="text-gray-300 mb-4">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-600 mb-2">কোনো ডোনার পাওয়া যায়নি!</h3>
                <p class="text-gray-500 max-w-md">আপনার দেওয়া ফিল্টারে এই মুহূর্তে কোনো ডোনার রক্ত দেওয়ার জন্য প্রস্তুত নেই। অনুগ্রহ করে অন্য এলাকা বা ফিল্টার দিয়ে আবার চেষ্টা করুন।</p>
            </div>
        @else
            {{-- Donors Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($donors as $donor)
                    @php
                        $donorId = $donor->id;
                        $challenge = session("reveal_challenge.$donorId");
                        $revealedPhone = session("revealed_phone.$donorId");
                        $target = session('reveal_target');
                        $masked = substr($donor->phone, 0, 3) . '****' . substr($donor->phone, -4);
                    @endphp

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 overflow-hidden group relative">
                        
                        {{-- Smart Priority Tags --}}
                        @if($donor->is_ready_now)
                            <div class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded-bl-lg uppercase tracking-wider z-10">
                                Ready Now
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="font-bold text-lg text-gray-900 group-hover:text-red-600 transition-colors">{{ $donor->name }}</h4>
                                        
                                        {{-- Verified Badges --}}
                                        @if($donor->verified_badge || $donor->nid_status === 'approved')
                                            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20" title="Verified Member"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @endif
                                    </div>
                                    
                                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                        {{ $donor->upazila?->name ?? 'উপজেলা' }}, {{ $donor->district?->name ?? 'জেলা' }}
                                    </p>
                                </div>
                                <div class="bg-red-50 text-red-600 font-black text-xl px-3 py-1 rounded-lg border border-red-100">
                                    {{ $donor->blood_group?->value ?? (string) $donor->blood_group }}
                                </div>
                            </div>

                            {{-- Security Logic: Phone Reveal & Math Challenge --}}
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 mt-2">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">মোবাইল নম্বর</span>
                                    <span class="font-mono font-bold text-gray-800 tracking-wider">
                                        {{ $revealedPhone ? $revealedPhone : $masked }}
                                    </span>
                                </div>

                                @if(!$revealedPhone)
                                    @if($target == $donorId && is_array($challenge))
                                        {{-- Challenge Form --}}
                                        <form method="POST" action="{{ route('donors.reveal.verify', $donorId) }}" class="space-y-2 mt-2">
                                            @csrf
                                            <label class="block text-xs font-bold text-red-600 bg-red-50 p-2 rounded border border-red-100">
                                                নিরাপত্তা প্রশ্ন: {{ $challenge['question'] }}
                                            </label>
                                            <div class="flex gap-2">
                                                <input type="number" name="answer" required class="flex-1 rounded-md border-gray-300 text-sm focus:border-red-500 focus:ring focus:ring-red-200" placeholder="যোগফল লিখুন">
                                                <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm font-bold hover:bg-gray-900 transition-colors">ভেরিফাই</button>
                                            </div>
                                        </form>
                                    @else
                                        {{-- 🎯 FIX: Secure Form Button for CSRF Token --}}
                                        <form method="POST" action="{{ route('donors.reveal.start', $donorId) }}">
                                            @csrf
                                            <button type="submit" class="w-full text-center py-2 border-2 border-dashed border-gray-300 text-gray-600 font-semibold rounded-lg text-sm hover:border-red-400 hover:text-red-600 transition-colors bg-white">
                                                নম্বর দেখতে ক্লিক করুন
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="tel:{{ $revealedPhone }}" class="block w-full text-center py-2 bg-green-50 text-green-700 font-bold rounded-lg text-sm hover:bg-green-100 transition-colors border border-green-200">
                                        কল করুন
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-8">
                {{ $donors->links() }}
            </div>
        @endif
    </div>

    {{-- 🎯 Smart Scroll Retention Logic --}}
    <script>
        // ১. যখনই কোনো ফর্ম (Math Challenge বা Reveal Button) সাবমিট হবে, স্ক্রল পজিশন সেভ করে রাখব
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                sessionStorage.setItem('donorScrollPosition', window.scrollY);
            });
        });

        // ২. পেজ রিলোড হওয়ার পর চেক করব কোনো সেভ করা স্ক্রল পজিশন আছে কি না
        document.addEventListener("DOMContentLoaded", function() {
            let scrollPos = sessionStorage.getItem('donorScrollPosition');
            if (scrollPos) {
                // পজিশন থাকলে ঠিক সেখানে স্ক্রল করে নিয়ে যাব (Smoothly)
                window.scrollTo({
                    top: parseInt(scrollPos),
                    behavior: 'instant' 
                });
                // কাজ শেষ, তাই মেমোরি থেকে মুছে ফেললাম যাতে অন্য পেজে গেলে সমস্যা না হয়
                sessionStorage.removeItem('donorScrollPosition');
            }
        });
    </script>
</div>
@endsection