@extends('layouts.app')

@section('title', 'প্রোফাইল সেটিংস — রক্তদূত')

@section('content')
<div class="bg-slate-50 min-h-screen py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-8">

        {{-- ১. পেজ হেডার --}}
        <div class="text-center sm:text-left">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 tracking-tight">প্রোফাইল সেটিংস</h1>
            <p class="text-slate-500 font-medium mt-2 text-sm sm:text-base">আপনার ব্যক্তিগত তথ্য, ডোনার স্ট্যাটাস এবং অ্যাকাউন্ট সেটিংস ম্যানেজ করুন।</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('status') === 'profile-updated' || session('success_msg'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-2xl flex items-center gap-3">
                <svg class="w-6 h-6 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success_msg') ?? 'প্রোফাইল সফলভাবে আপডেট হয়েছে!' }}
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-2xl flex items-center gap-3">
                <svg class="w-6 h-6 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                পাসওয়ার্ড সফলভাবে পরিবর্তিত হয়েছে!
            </div>
        @endif
        @if(session('bonus_msg'))
            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 font-bold rounded-2xl flex items-center gap-3">
                <span class="text-xl">🏆</span>
                {{ session('bonus_msg') }}
            </div>
        @endif
        @if(session('status') === 'emergency-updated' || session('emergency_msg'))
            <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 font-bold rounded-2xl flex items-center gap-3">
                <span class="text-xl">⚡</span>
                {{ session('emergency_msg') ?? 'স্ট্যাটাস আপডেট হয়েছে!' }}
            </div>
        @endif

        {{-- ২. প্রোফাইল কমপ্লিশন কার্ড --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="bg-slate-900 px-6 py-5 flex items-center justify-between">
                <div>
                    <h2 class="text-white font-extrabold text-lg">প্রোফাইল কমপ্লিশন</h2>
                    <p class="text-slate-400 text-sm mt-1">তথ্য সম্পূর্ণ করে আপনার প্রোফাইল শক্তিশালী করুন</p>
                </div>
                <div class="flex flex-col items-end min-w-[100px]">
                    <span class="text-2xl font-black text-white">{{ $completionPercent }}%</span>
                    <div class="w-full bg-slate-700 h-2.5 rounded-full mt-2 overflow-hidden">
                        <div class="bg-green-500 h-full transition-all duration-1000" style="width: {{ $completionPercent }}%"></div>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($completionSteps as $step)
                    <div class="flex items-center gap-3 py-2">
                        @if($step['done'])
                            <div class="w-7 h-7 rounded-full bg-green-50 flex items-center justify-center shrink-0 border border-green-200">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-sm font-bold text-slate-800">{{ $step['label'] }}</span>
                        @else
                            <div class="w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center shrink-0 border border-slate-200">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-500">{{ $step['label'] }}</span>
                            <span class="ml-auto text-xs font-bold text-slate-400">+{{ $step['weight'] }}%</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @if($completionPercent >= 100)
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-xl text-center">
                        <span class="font-bold text-green-700 text-sm">🎉 অভিনন্দন! আপনার প্রোফাইল ১০০% সম্পূর্ণ! (বোনাস পয়েন্ট প্রদান করা হয়েছে)</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ৩. অ্যাভেইলেবল স্ট্যাটাস কার্ড (Alpine.js + Axios AJAX) --}}
        <div x-data="emergencyToggle({{ $user->is_available ? 'true' : 'false' }})" 
             :class="isAvailable ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200'"
             class="rounded-2xl border p-6 flex flex-col sm:flex-row items-center justify-between gap-6 transition-colors duration-300 relative overflow-hidden">
            
            <!-- Loading indicator -->
            <div x-show="isLoading" style="display: none;" class="absolute inset-0 bg-white/40 backdrop-blur-[1px] z-10 flex items-center justify-center">
                <svg class="animate-spin h-6 w-6 text-slate-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>

            <div class="flex items-center gap-4 relative z-0">
                <div :class="isAvailable ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600'" 
                     class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <div>
                    <h2 :class="isAvailable ? 'text-green-900' : 'text-yellow-900'" 
                        class="text-lg font-bold transition-colors">অ্যাভেইলেবল স্ট্যাটাস</h2>
                    <p :class="isAvailable ? 'text-green-700' : 'text-yellow-700'" 
                       x-text="isAvailable ? 'আপনি বর্তমানে রক্তদানের জন্য প্রস্তুত আছেন এবং নোটিফিকেশন পাবেন।' : 'আপনি বর্তমানে রক্তদানের জন্য এক্টিভ নন। ইমার্জেন্সি রিকোয়েস্ট পেতে স্ট্যাটাস চালু করুন।'"
                       class="text-sm font-medium mt-1 transition-colors"></p>
                </div>
            </div>
            
            <button type="button" @click="toggleStatus" 
                    :disabled="isLoading"
                    :class="isAvailable ? 'bg-green-500' : 'bg-slate-300'"
                    class="relative w-16 h-8 rounded-full transition-colors focus:outline-none shrink-0 z-0 disabled:opacity-50 cursor-pointer shadow-inner">
                <span :class="isAvailable ? 'translate-x-8' : 'translate-x-0'" 
                      class="absolute top-1 left-1 transform transition-transform duration-300 w-6 h-6 bg-white rounded-full shadow"></span>
            </button>
        </div>

        {{-- ৪. ব্যক্তিগত ও ডোনার তথ্য কার্ড --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-6 border-b border-slate-100 pb-4">ব্যক্তিগত ও ডোনার তথ্য</h2>
            
            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('patch')

                {{-- Avatar Upload UI --}}
                <div class="flex items-center gap-5 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border border-slate-200 bg-white overflow-hidden shrink-0 flex items-center justify-center">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">প্রোফাইল ছবি আপলোড</label>
                        <input type="file" name="profile_image" accept="image/*" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-900 cursor-pointer transition">
                        @error('profile_image') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- ২ కলাম গ্রিড --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">পূর্ণ নাম <span class="text-red-500">*</span></label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}" placeholder="আপনার নাম লিখুন" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                        @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ইমেইল অ্যাড্রেস <span class="text-red-500">*</span></label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="ইমেইল অ্যাড্রেস" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                        @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">মোবাইল নম্বর <span class="text-red-500">*</span></label>
                        <input name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="যেমন: 01XXXXXXXXX" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                        @error('phone') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="blood_group" required class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 pr-10 cursor-pointer transition outline-none">
                                <option value="">নির্বাচন করুন</option>
                                @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group', $user->blood_group?->value ?? $user->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('blood_group') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- লোকেশনের ৩টি ড্রপডাউন --}}
                <div class="pt-2">
                    <x-location-selector
                        :selected-division="old('division_id', $user->division_id)"
                        :selected-district="old('district_id', $user->district_id)"
                        :selected-upazila="old('upazila_id', $user->upazila_id)"
                    />
                </div>

                {{-- আরো ৪টি ফিল্ড --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">শেষ রক্তদানের তারিখ</label>
                        <input name="last_donation_date" type="date" value="{{ old('last_donation_date', $user->last_donation_date?->format('Y-m-d') ?? $user->last_donation_date) }}" max="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 cursor-pointer transition outline-none">
                        @error('last_donation_date') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">লিঙ্গ</label>
                        <div class="relative">
                            <select name="gender" class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 pr-10 cursor-pointer transition outline-none">
                                <option value="">নির্বাচন করুন</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>পুরুষ</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>মহিলা</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('gender') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ওজন (কেজি)</label>
                        <input name="weight" type="number" step="0.1" value="{{ old('weight', $user->weight) }}" placeholder="যেমন: 65" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                        @error('weight') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">অর্গানাইজেশন/ক্লাব</label>
                        <div class="relative">
                            <select name="organization_id" class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 pr-10 cursor-pointer transition outline-none">
                                <option value="">কোনো ক্লাবের সাথে যুক্ত নই</option>
                                @if(isset($organizations))
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>{{ $org->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('organization_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm px-8 py-3.5 rounded-xl shadow-lg transition">
                        সেভ করুন
                    </button>
                </div>
            </form>
        </div>

        {{-- ৫. আইডেন্টিটি ভেরিফিকেশন (NID) কার্ড --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 relative overflow-hidden shadow-sm">
            @php $nidStatus = $user->nid_status ?? 'unverified'; @endphp
            
            <div class="absolute right-6 top-6">
                @if($nidStatus === 'unverified')
                    <span class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded-lg text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span> Unverified
                    </span>
                @elseif($nidStatus === 'pending')
                    <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-600 border border-amber-200 px-3 py-1 rounded-lg text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span> Pending
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-600 border border-green-200 px-3 py-1 rounded-lg text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span> Verified
                    </span>
                @endif
            </div>

            <h2 class="text-xl font-bold text-slate-800 mb-1">আইডেন্টিটি ভেরিফিকেশন</h2>
            <p class="text-sm font-medium text-slate-500 mb-6 border-b border-slate-100 pb-4">প্ল্যাটফর্মে Verified ব্যাজ পেতে আপনার NID প্রদান করুন।</p>
            
            <form method="POST" action="{{ route('donor.upload_nid') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">NID নম্বর <span class="text-red-500">*</span></label>
                        <input name="nid_number" type="text" value="{{ old('nid_number', $user->nid_number ?? '') }}" placeholder="আপনার NID নম্বর লিখুন" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                        @error('nid_number') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">NID-এর ছবি আপলোড করুন</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-[3.25rem] bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="text-sm font-bold text-slate-600">ফাইল নির্বাচন করুন</p>
                                </div>
                                <input type="file" name="nid_document" accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                            </label>
                        </div>
                        @error('nid_document') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm px-8 py-3 rounded-xl transition">
                        সাবমিট করুন
                    </button>
                </div>
            </form>
        </div>

        {{-- ৬. পাসওয়ার্ড পরিবর্তন কার্ড --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 mb-6 border-b border-slate-100 pb-4">পাসওয়ার্ড পরিবর্তন</h2>
            <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-lg">
                @csrf
                @method('put')
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">বর্তমান পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input name="current_password" type="password" placeholder="বর্তমান পাসওয়ার্ড দিন" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                    @error('current_password', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">নতুন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input name="password" type="password" placeholder="নতুন পাসওয়ার্ড দিন" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                    @error('password', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">পুনরায় নতুন পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input name="password_confirmation" type="password" placeholder="আবার নতুন পাসওয়ার্ড দিন" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-slate-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                    @error('password_confirmation', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="pt-2">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm px-6 py-3 rounded-xl transition">
                        পাসওয়ার্ড আপডেট করুন
                    </button>
                </div>
            </form>
        </div>

        {{-- ৭. অ্যাকাউন্ট ডিলিট (Danger Zone) --}}
        <div class="bg-white rounded-2xl border border-red-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-xl font-bold text-red-600 mb-2">অ্যাকাউন্ট মুছে ফেলুন</h2>
            <p class="text-sm font-medium text-slate-500 mb-6 border-b border-red-50 pb-4 max-w-2xl">স্থায়ীভাবে আপনার অ্যাকাউন্ট মুছে ফেলতে চান? এটি করার পর আর কোনোভাবেই তথ্যগুলো ফিরে পাওয়া যাবে না।</p>
            
            <form method="post" action="{{ route('profile.destroy') }}" class="max-w-lg">
                @csrf
                @method('delete')
                <div class="mb-5">
                    <label class="block text-sm font-bold text-slate-700 mb-2">অ্যাকাউন্ট মুছতে পাসওয়ার্ড নিশ্চিত করুন</label>
                    <input name="password" type="password" placeholder="আপনার পাসওয়ার্ড দিন" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-red-400 focus:ring-0 px-4 py-3 font-medium text-slate-800 transition outline-none">
                    @error('password', 'userDeletion') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>
                <button type="submit" onclick="return confirm('আপনি কি নিশ্চিত যে আপনার অ্যাকাউন্ট মুছে ফেলতে চান?')" class="bg-red-600 hover:bg-red-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    অ্যাকাউন্ট মুছে ফেলুন
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('emergencyToggle', (initialStatus) => ({
            isAvailable: initialStatus,
            isLoading: false,

            async toggleStatus() {
                // Optional: ask for confirmation gracefully? Not strict in AJAX toggle usually, skipping native confirm
                this.isLoading = true;
                
                try {
                    const response = await axios.post('{{ route('profile.emergency.toggle') }}', {}, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    });
                    
                    if(response.data.success) {
                        this.isAvailable = response.data.is_available;
                        // Show success alert
                        alert(response.data.message);
                    }
                } catch (error) {
                    console.error('API Error:', error);
                    alert('স্ট্যাটাস পরিবর্তনে সমস্যা হয়েছে। দয়া করে আবার চেষ্টা করুন।');
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>
@endsection