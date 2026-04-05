@extends('layouts.app')

@section('title', 'প্রোফাইল সেটিংস — রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">প্রোফাইল সেটিংস</h1>
        <p class="text-slate-500 font-medium mt-2">আপনার ব্যক্তিগত তথ্য, ডোনার ডিটেইলস এবং পাসওয়ার্ড আপডেট করুন।</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-xl flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            প্রোফাইল সফলভাবে আপডেট হয়েছে!
        </div>
    @endif

    <div class="space-y-8">
        
        {{-- 🎯 ১. কমপ্লিট প্রোফাইল ও ডোনার ইনফরমেশন আপডেট ফর্ম --}}
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-red-600"></div>
            <header class="mb-6">
                <h2 class="text-xl font-extrabold text-slate-900">ব্যক্তিগত ও ডোনার তথ্য</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">রক্তদাতা হতে চাইলে ফর্মের সকল ফিল্ড সঠিকভাবে পূরণ করুন।</p>
            </header>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('patch')

                {{-- 📸 Profile Image Section --}}
                <div class="flex items-center gap-6 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="w-20 h-20 shrink-0 bg-slate-200 rounded-full border-4 border-white shadow-sm overflow-hidden flex items-center justify-center">
                        @if($user->profile_image)
                            <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-slate-900 mb-1">প্রোফাইল ছবি আপলোড করুন</label>
                        <input type="file" name="profile_image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-slate-800 file:text-white hover:file:bg-slate-900 transition cursor-pointer">
                        <p class="text-xs text-slate-400 mt-1 font-medium">সর্বোচ্চ ২ মেগাবাইট (JPG বা PNG)</p>
                        @error('profile_image') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">পূর্ণ নাম <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" 
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">ইমেইল অ্যাড্রেস <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">মোবাইল নাম্বার</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" placeholder="01XXXXXXXXX"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        @error('phone') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="blood_group" class="block text-sm font-bold text-slate-700 mb-2">রক্তের গ্রুপ</label>
                        <select id="blood_group" name="blood_group" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                            <option value="">নির্বাচন করুন</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group', $user->blood_group?->value ?? $user->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                        @error('blood_group') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- 📍 Dynamic Location Fields (Using Global Component) --}}
                <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                    <label class="block text-sm font-bold text-slate-700 mb-3">লোকেশন তথ্য</label>
                    <x-location-selector 
                        :selected-division="old('division_id', $user->division_id)"
                        :selected-district="old('district_id', $user->district_id)"
                        :selected-upazila="old('upazila_id', $user->upazila_id)"
                    />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-bold text-slate-700 mb-2">জন্ম তারিখ</label>
                        <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d') ?? $user->date_of_birth) }}" max="{{ date('Y-m-d') }}"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        @error('date_of_birth') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-bold text-slate-700 mb-2">লিঙ্গ</label>
                        <select id="gender" name="gender" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                            <option value="">নির্বাচন করুন</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>পুরুষ</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>মহিলা</option>
                        </select>
                        @error('gender') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-bold text-slate-700 mb-2">ওজন (কেজি)</label>
                        <input id="weight" name="weight" type="number" step="0.1" value="{{ old('weight', $user->weight) }}" placeholder="যেমন: 65"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        @error('weight') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- 🏢 Organization Section --}}
                <div class="pt-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">অর্গানাইজেশন/ব্লাড ক্লাব</label>
                    <select name="organization_id" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        <option value="">কোনো ব্লাড ক্লাবের সাথে যুক্ত নই</option>
                        @if(isset($organizations))
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" @selected(old('organization_id', $user->organization_id) == $org->id)>{{ $org->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p class="text-xs text-slate-500 mt-1 font-medium">অর্গানাইজেশন পরিবর্তন করলে আপনার ভেরিফাইড ব্যাজ পুনরায় যাচাই করা হবে।</p>
                </div>

                {{-- ইমেইল ভেরিফিকেশন মেসেজ --}}
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                        <p class="text-sm font-semibold text-amber-800">
                            আপনার ইমেইল অ্যাড্রেসটি ভেরিফাইড নয়।
                            <button form="send-verification" class="underline text-red-600 hover:text-red-800 font-extrabold ml-1 transition">
                                ভেরিফিকেশন ইমেইল আবার পাঠাতে এখানে ক্লিক করুন।
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-xs font-black text-emerald-600">
                                একটি নতুন ভেরিফিকেশন লিংক আপনার ইমেইলে পাঠানো হয়েছে।
                            </p>
                        @endif
                    </div>
                @endif

                <div class="flex items-center gap-4 pt-6 border-t border-slate-100">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3.5 rounded-xl text-sm font-extrabold transition shadow-sm">
                        সেভ করুন
                    </button>
                </div>
            </form>
        </div>

        {{-- 🎯 ২. পাসওয়ার্ড পরিবর্তন ফর্ম --}}
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-2 h-full bg-slate-800"></div>
            <header class="mb-6">
                <h2 class="text-xl font-extrabold text-slate-900">পাসওয়ার্ড পরিবর্তন</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">অ্যাকাউন্টের নিরাপত্তা নিশ্চিত করতে একটি শক্তিশালী পাসওয়ার্ড ব্যবহার করুন।</p>
            </header>

            <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('put')

                <div>
                    <label for="update_password_current_password" class="block text-sm font-bold text-slate-700 mb-2">বর্তমান পাসওয়ার্ড</label>
                    <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 font-semibold text-slate-800 px-4 py-3">
                    @error('current_password', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="update_password_password" class="block text-sm font-bold text-slate-700 mb-2">নতুন পাসওয়ার্ড</label>
                    <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 font-semibold text-slate-800 px-4 py-3">
                    @error('password', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="update_password_password_confirmation" class="block text-sm font-bold text-slate-700 mb-2">কনফার্ম নতুন পাসওয়ার্ড</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                           class="w-full rounded-xl border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 font-semibold text-slate-800 px-4 py-3">
                    @error('password_confirmation', 'updatePassword') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3.5 rounded-xl text-sm font-extrabold transition shadow-sm">
                        পাসওয়ার্ড আপডেট করুন
                    </button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-extrabold text-emerald-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            পাসওয়ার্ড পরিবর্তিত হয়েছে!
                        </p>
                    @endif
                </div>
            </form>
        </div>

        {{-- 🚪 ৩. লগআউট সেকশন --}}
        <div class="bg-red-50 p-6 sm:p-8 rounded-3xl border border-red-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-lg font-black text-red-900">অ্যাকাউন্ট থেকে বের হতে চান?</h3>
                <p class="text-sm text-red-700 font-medium mt-1">আপনি যেকোনো সময় আপনার ইমেইল ও পাসওয়ার্ড দিয়ে পুনরায় লগইন করতে পারবেন।</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="w-full md:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-xl font-extrabold shadow-sm transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    লগআউট করুন
                </button>
            </form>
        </div>

    </div>
</div>
@endsection