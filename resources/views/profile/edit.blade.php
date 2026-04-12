@extends('layouts.app')

@section('title', 'প্রোফাইল সেটিংস — রক্তদূত')

@section('content')
<div class="bg-[#f8fafc] min-h-screen py-10 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Abstract Background Decorators -->
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-red-50/80 to-transparent pointer-events-none"></div>
    <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] rounded-full bg-red-100/30 blur-[120px] pointer-events-none"></div>
    <div class="absolute top-[20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-blue-100/20 blur-[100px] pointer-events-none"></div>

    <div class="max-w-4xl mx-auto space-y-10 relative z-10">

        {{-- ১. পেজ হেডার --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left mb-6">
            <div class="w-16 h-16 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center justify-center shrink-0 relative">
                <div class="absolute inset-0 bg-red-500/10 rounded-2xl rotate-3 scale-105 -z-10"></div>
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">প্রোফাইল সেটিংস</h1>
                <p class="text-slate-500 font-medium mt-2 text-sm sm:text-base">আপনার ব্যক্তিগত তথ্য, ডোনার স্ট্যাটাস এবং অ্যাকাউন্ট সেটিংস ম্যানেজ করুন</p>
            </div>
        </div>

        {{-- Flash Messages (Glass Effect) --}}
        @if(session('status') === 'profile-updated' || session('success_msg'))
            <div class="p-4 bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-800 font-bold rounded-2xl flex items-center gap-3 shadow-sm transform animate-[fadeIn_0.5s_ease-out]">
                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                {{ session('success_msg') ?? 'প্রোফাইল সফলভাবে আপডেট হয়েছে!' }}
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="p-4 bg-emerald-50/80 backdrop-blur-md border border-emerald-200 text-emerald-800 font-bold rounded-2xl flex items-center gap-3 shadow-sm transform animate-[fadeIn_0.5s_ease-out]">
                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                পাসওয়ার্ড সফলভাবে পরিবর্তিত হয়েছে!
            </div>
        @endif
        @if(session('bonus_msg'))
            <div class="p-4 bg-amber-50/80 backdrop-blur-md border border-amber-200 text-amber-900 font-bold rounded-2xl flex items-center gap-3 shadow-sm transform animate-[fadeIn_0.5s_ease-out]">
                <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center text-xl shrink-0">🏆</div>
                {{ session('bonus_msg') }}
            </div>
        @endif
        @if(session('status') === 'emergency-updated' || session('emergency_msg'))
            <div class="p-4 bg-blue-50/80 backdrop-blur-md border border-blue-200 text-blue-800 font-bold rounded-2xl flex items-center gap-3 shadow-sm transform animate-[fadeIn_0.5s_ease-out]">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-xl shrink-0">⚡</div>
                {{ session('emergency_msg') ?? 'স্ট্যাটাস আপডেট হয়েছে!' }}
            </div>
        @endif

        @if($completionPercent < 100)
            {{-- ২. প্রোফাইল কমপ্লিশন কার্ড (Premium Dark UI) --}}
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl border border-slate-700/50 overflow-hidden shadow-xl shadow-slate-900/10 mb-10">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <h2 class="text-white font-extrabold text-xl tracking-wide">প্রোফাইল কমপ্লিশন</h2>
                            </div>
                            <p class="text-slate-400 text-sm font-medium">তথ্য সম্পূর্ণ করে আপনার প্রোফাইল শক্তিশালী করুন এবং টপ ডোনার লিস্টে যুক্ত হোন</p>
                        </div>
                        <div class="flex flex-col w-full sm:w-1/3">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-slate-400 text-xs font-bold uppercase tracking-wider">Progress</span>
                                <span class="text-2xl font-black text-white">{{ $completionPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-700/50 h-3 rounded-full overflow-hidden backdrop-blur-sm border border-slate-600/50">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-400 h-full transition-all duration-1000 relative" style="width: {{ $completionPercent }}%">
                                    <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite]"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                        @foreach($completionSteps as $step)
                        <div class="flex items-center gap-3 p-3 rounded-2xl transition-all {{ $step['done'] ? 'bg-white/5 border border-white/10' : 'hover:bg-white/5 border border-transparent' }}">
                            @if($step['done'])
                                <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center shrink-0 border border-green-500/30">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-sm font-semibold text-slate-200">{{ $step['label'] }}</span>
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700">
                                    <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                                </div>
                                <span class="text-sm font-medium text-slate-500">{{ $step['label'] }}</span>
                                <span class="ml-auto text-xs font-bold text-slate-600 px-2.5 py-1 bg-slate-800 rounded-lg border border-slate-700">+{{ $step['weight'] }}%</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            {{-- ১০০% সম্পূর্ণ হওয়ার পর সাকসেস ব্যাজ (Sleek Compact UI) --}}
            <div class="flex justify-center mb-10">
                <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/80 backdrop-blur-xl border border-emerald-200/60 rounded-2xl shadow-[0_8px_30px_rgb(16,185,129,0.08)] transform transition-all hover:scale-[1.02]">
                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-extrabold text-emerald-900 tracking-wide text-sm sm:text-base">✅ আপনার Profile ১০০% সম্পূর্ণ এবং ভেরিফাইড।</span>
                </div>
            </div>
        @endif

        {{-- ৩. অ্যাভেইলেবল স্ট্যাটাস কার্ড (Premium Alpine.js Toggle) --}}
        <div x-data="emergencyToggle({{ $user->is_available ? 'true' : 'false' }})" 
             :class="isAvailable ? 'bg-gradient-to-r from-emerald-50 to-white border-emerald-200 shadow-[0_8px_30px_rgb(16,185,129,0.12)]' : 'bg-gradient-to-r from-slate-50 to-white border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)]'"
             class="rounded-3xl border p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 transition-all duration-500 relative overflow-hidden group">
            
            <div x-show="isLoading" style="display: none;" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
                <div class="flex flex-col items-center gap-3">
                    <svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span class="text-sm font-bold text-slate-700">আপডেট হচ্ছে...</span>
                </div>
            </div>

            <div class="flex items-center gap-5 relative z-0 w-full sm:w-auto">
                <div :class="isAvailable ? 'bg-emerald-100 text-emerald-600 ring-4 ring-emerald-50 shadow-inner' : 'bg-slate-100 text-slate-400 ring-4 ring-slate-50'" 
                     class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 transition-all duration-500 relative">
                     <span :class="isAvailable ? 'animate-ping opacity-30 bg-emerald-400' : 'hidden'" class="absolute inset-0 rounded-2xl"></span>
                    <svg class="w-7 h-7 relative z-10" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <div class="flex-1">
                    <h2 :class="isAvailable ? 'text-emerald-900' : 'text-slate-800'" class="text-xl font-extrabold transition-colors">অ্যাভেইলেবল স্ট্যাটাস</h2>
                    <p :class="isAvailable ? 'text-emerald-700' : 'text-slate-500'" 
                       x-text="isAvailable ? 'আপনি বর্তমানে রক্তদানের জন্য সম্পূর্ণ প্রস্তুত। ইমার্জেন্সি নোটিফিকেশন চালু আছে।' : 'আপনি বর্তমানে রক্তদানের জন্য এক্টিভ নন। ব্লাড রিকোয়েস্ট পেতে টগলটি চালু করুন।'"
                       class="text-sm font-semibold mt-1 transition-colors leading-relaxed"></p>
                </div>
            </div>
            
            <div class="shrink-0">
                <button type="button" @click="toggleStatus" 
                        :disabled="isLoading"
                        :class="isAvailable ? 'bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.4)]' : 'bg-slate-200 hover:bg-slate-300'"
                        class="relative w-20 h-10 rounded-full transition-all duration-300 focus:outline-none disabled:opacity-50 cursor-pointer flex items-center p-1">
                    <span :class="isAvailable ? 'translate-x-10 bg-white' : 'translate-x-0 bg-white'" 
                          class="transform transition-transform duration-300 w-8 h-8 rounded-full shadow-lg flex items-center justify-center">
                          <svg x-show="isAvailable" class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                          <svg x-show="!isAvailable" class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                    </span>
                </button>
            </div>
        </div>

        {{-- ৪. ব্যক্তিগত ও ডোনার তথ্য কার্ড (Glass Card) --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-200 hover:border-slate-300 p-6 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
            <div class="flex items-center gap-3 mb-8 border-b border-slate-100 pb-5">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">ব্যক্তিগত ও ডোনার তথ্য</h2>
            </div>
            
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-7">
                @csrf
                @method('patch')

                {{-- Premium Avatar Upload UI --}}
                <div class="flex flex-col sm:flex-row items-center gap-6 p-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-red-50/30 hover:border-red-200 transition-all group">
                    <div class="w-24 h-24 rounded-full border-4 border-white overflow-hidden shrink-0 flex items-center justify-center shadow-lg group-hover:shadow-red-500/20 transition-all">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <h3 class="text-base font-bold text-slate-800 mb-1">প্রোফাইল ছবি</h3>
                        <p class="text-sm font-medium text-slate-500 mb-4">একটি সুন্দর ছবি আপনার প্রোফাইলকে আরও বিশ্বস্ত করে তোলে (Max: 2MB)</p>
                        
                        <label class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-700 cursor-pointer shadow-sm hover:shadow hover:border-red-300 hover:text-red-600 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            ছবি নির্বাচন করুন
                            <input type="file" name="profile_image" accept="image/*" class="hidden">
                        </label>
                        @error('profile_image') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Input Fields Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-7">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">পূর্ণ নাম <span class="text-red-500">*</span></label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="আপনার নাম লিখুন" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ইমেইল অ্যাড্রেস <span class="text-red-500">*</span></label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="name@example.com" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none">
                        @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">মোবাইল নম্বর <span class="text-red-500">*</span></label>
                        <input name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="উদাহরণ: 01XXXXXXXXX" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none">
                        @error('phone') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="blood_group" required class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-bold pr-10 cursor-pointer transition-all outline-none">
                                <option value="">নির্বাচন করুন</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $user->blood_group?->value ?? $user->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('blood_group') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Location Section using component --}}
                <div class="pt-2 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        লোকেশান তথ্য
                    </h3>
                    <x-location-selector
                        :selected-division="old('division_id', $user->division_id)"
                        :selected-district="old('district_id', $user->district_id)"
                        :selected-upazila="old('upazila_id', $user->upazila_id)"
                    />
                </div>

                {{-- Additional Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-7 pt-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">জন্ম তারিখ</label>
                        <input name="date_of_birth" type="date" value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none cursor-pointer">
                        @error('date_of_birth') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">শেষ রক্তদানের তারিখ</label>
                        @php
                            $hasVerifiedDonation = \App\Models\BloodRequestResponse::where('user_id', auth()->id())->whereNotNull('fulfilled_at')->exists();
                        @endphp
                        <input name="last_donation_date" type="date" value="{{ old('last_donation_date', $user->last_donation_date?->format('Y-m-d') ?? $user->last_donation_date) }}" max="{{ date('Y-m-d') }}" {{ $hasVerifiedDonation ? 'readonly' : '' }} class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium {{ $hasVerifiedDonation ? 'cursor-not-allowed opacity-70' : 'cursor-pointer' }} transition-all outline-none">
                        @if($hasVerifiedDonation)
                            <p class="text-[11px] font-semibold text-slate-500 mt-1.5 flex items-start gap-1">
                                <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                আপনার সর্বশেষ রক্তদানের রেকর্ডের ভিত্তিতে এটি স্বয়ংক্রিয়ভাবে নিয়ন্ত্রিত।
                            </p>
                        @endif
                        @error('last_donation_date') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">লিঙ্গ</label>
                        <div class="relative">
                            <select name="gender" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium pr-10 cursor-pointer transition-all outline-none">
                                <option value="">নির্বাচন করুন</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>পুরুষ</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>মহিলা</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('gender') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ওজন (কেজি)</label>
                        <input name="weight" type="number" step="0.1" value="{{ old('weight', $user->weight) }}" placeholder="যেমন: 65" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none">
                        @error('weight') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">অর্গানাইজেশন/ক্লাব</label>
                        <div class="relative">
                            <select name="organization_id" class="w-full appearance-none rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium pr-10 cursor-pointer transition-all outline-none">
                                <option value="">কোনো ক্লাবের সাথে যুক্ত নই</option>
                                @if(isset($organizations))
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>{{ $org->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('organization_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-8">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-base px-10 py-3.5 rounded-xl shadow-[0_8px_20px_-6px_rgba(220,38,38,0.5)] transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-2 w-full sm:w-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        সেভ পরিবর্তনসমূহ
                    </button>
                </div>
            </form>
        </div>

        {{-- ৫. আইডেন্টিটি ভেরিফিকেশন (NID) কার্ড (Premium UI) --}}
        @php 
            $nidStatus = strtolower($user->nid_status ?? 'unverified'); 
            if($nidStatus === 'approved') $nidStatus = 'verified';
        @endphp
        
        @if($nidStatus !== 'verified')
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-200 hover:border-slate-300 p-6 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 relative overflow-hidden">
            
            <div class="absolute right-0 top-0 m-6 sm:m-10">
                @if($nidStatus === 'unverified' || empty($nidStatus))
                    <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-xl text-sm font-bold shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Unverified
                    </span>
                @elseif($nidStatus === 'pending')
                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 border border-amber-200 px-4 py-2 rounded-xl text-sm font-bold shadow-sm">
                        <svg class="w-4 h-4 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pending Review
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">আইডেন্টিটি ভেরিফিকেশন</h2>
            </div>
            
            <p class="text-sm font-medium text-slate-500 mb-8 pb-5 border-b border-slate-100 max-w-2xl">
                প্ল্যাটফর্মে 'Verified' ব্যাজ পেতে আপনার সঠিক NID নম্বর অথবা ডকুমেন্টের ছবি যেকোনো একটি প্রদান করুন। এটি আপনার প্রোফাইলের বিশ্বাসযোগ্যতা বাড়াবে।
            </p>
            
            <form method="POST" action="{{ route('donor.upload_nid') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-7">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">NID নম্বর</label>
                        <input name="nid_number" type="text" value="{{ old('nid_number', $user->nid_number ?? '') }}" placeholder="আপনার ১০ বা ১৭ ডিজিটের NID নম্বর" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none">
                        @error('nid_number') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">NID ডকুমেন্টের ছবি</label>
                        <div class="relative group">
                            <label class="flex flex-col items-center justify-center w-full h-[3.25rem] bg-slate-50/50 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:bg-slate-50 hover:border-red-300 transition-all">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5 text-slate-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-red-600 transition-colors" id="fileNameDisplay">ফাইল নির্বাচন করুন</span>
                                </div>
                                <input type="file" name="nid_document" accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="document.getElementById('fileNameDisplay').textContent = this.files[0]?.name || 'ফাইল নির্বাচন করুন'">
                            </label>
                        </div>
                        @error('nid_document') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-[11px] font-semibold text-slate-400 mt-2">Allowed: JPG, PNG, PDF (Max 2MB)</p>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-base px-8 py-3 rounded-xl shadow-[0_8px_20px_-6px_rgba(30,41,59,0.5)] transition-all hover:-translate-y-1 active:scale-95">
                        সাবমিট করুন
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- ৬. পাসওয়ার্ড পরিবর্তন কার্ড --}}
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-200 hover:border-slate-300 p-6 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300">
            <div class="flex items-center gap-3 mb-8 border-b border-slate-100 pb-5">
                <div class="w-10 h-10 bg-slate-100 text-slate-700 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">পাসওয়ার্ড পরিবর্তন</h2>
            </div>
            
            <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('put')
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">বর্তমান পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input name="current_password" type="password" placeholder="••••••••" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none tracking-widest">
                    @error('current_password', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">নতুন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                        <input name="password" type="password" placeholder="••••••••" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none tracking-widest">
                        @error('password', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">পুনরায় নতুন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                        <input name="password_confirmation" type="password" placeholder="••••••••" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-red-600 focus:ring-4 focus:ring-red-600/10 px-4 py-3 text-slate-800 font-medium transition-all outline-none tracking-widest">
                        @error('password_confirmation', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-base px-8 py-3.5 rounded-xl shadow-[0_8px_20px_-6px_rgba(30,41,59,0.5)] transition-all hover:-translate-y-1 active:scale-95">
                        পাসওয়ার্ড আপডেট করুন
                    </button>
                </div>
            </form>
        </div>

        {{-- ৭. অ্যাকাউন্ট ডিলিট (Danger Zone) --}}
        <div class="bg-gradient-to-br from-white to-red-50/30 rounded-3xl border border-red-100 hover:border-red-200 p-6 sm:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/5 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="flex items-center gap-3 mb-3 relative z-10">
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-2xl font-bold text-red-600">ডেঞ্জার জোন: অ্যাকাউন্ট মুছুন</h2>
            </div>
            
            <p class="text-sm font-semibold text-red-800/70 mb-8 max-w-2xl relative z-10">স্থায়ীভাবে আপনার অ্যাকাউন্ট মুছে ফেলতে চান? এটি করার পর আপনার সমস্ত ডেটা, রেকর্ড এবং ব্যাজ চিরতরে ডিলিট হয়ে যাবে এবং আর ফিরে পাওয়া সম্ভব নয়।</p>
            
            <form method="post" action="{{ route('profile.destroy') }}" class="max-w-xl relative z-10">
                @csrf
                @method('delete')
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">নিশ্চিত করতে পাসওয়ার্ড দিন</label>
                    <input name="password" type="password" placeholder="••••••••" required class="w-full rounded-xl border border-red-200 bg-white focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/20 px-4 py-3 text-slate-800 font-medium transition-all outline-none tracking-widest">
                    @error('password', 'userDeletion') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত যে আপনার অ্যাকাউন্ট মুছে ফেলতে চান? এটি আর ফিরিয়ে আনা সম্ভব নয়।')" class="bg-red-600 hover:bg-red-700 text-white font-bold text-base px-8 py-3.5 rounded-xl shadow-[0_8px_20px_-6px_rgba(220,38,38,0.5)] transition-all hover:-translate-y-1 active:scale-95 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    স্থায়ীভাবে অ্যাকাউন্ট মুছে ফেলুন
                </button>
            </form>
        </div>

    </div>
</div>

<style>
/* Custom animations for premium feel */
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('emergencyToggle', (initialStatus) => ({
            isAvailable: initialStatus,
            isLoading: false,

            async toggleStatus() {
                this.isLoading = true;
                
                try {
                    const response = await axios.post('{{ route('donor_profile.is_available_now') }}', {}, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    });
                    
                    if(response.data.success) {
                        this.isAvailable = response.data.is_available;
                    }
                } catch (error) {
                    console.error('API Error:', error);
                    alert('স্ট্যাটাস পরিবর্তনে সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।');
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>
@endsection