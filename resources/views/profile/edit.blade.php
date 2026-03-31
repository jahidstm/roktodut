@extends('layouts.app')

@section('title', 'প্রোফাইল সেটিংস — রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">প্রোফাইল সেটিংস</h1>
        <p class="text-slate-500 font-medium mt-2">আপনার ব্যক্তিগত তথ্য, ডোনার ডিটেইলস এবং পাসওয়ার্ড আপডেট করুন।</p>
    </div>

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

            <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

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
                                <option value="{{ $bg }}" {{ old('blood_group', $user->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                        @error('blood_group') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- 📍 Dynamic Location Fields --}}
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="division_id" class="block text-sm font-bold text-slate-700 mb-2">বিভাগ</label>
                            <select id="division_id" name="division_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                                <option value="">নির্বাচন করুন</option>
                            </select>
                            @error('division_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="district_id" class="block text-sm font-bold text-slate-700 mb-2">জেলা</label>
                            <select id="district_id" name="district_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3" disabled>
                                <option value="">প্রথমে বিভাগ নির্বাচন করুন</option>
                            </select>
                            @error('district_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="upazila_id" class="block text-sm font-bold text-slate-700 mb-2">থানা/উপজেলা</label>
                            <select id="upazila_id" name="upazila_id" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3" disabled>
                                <option value="">প্রথমে জেলা নির্বাচন করুন</option>
                            </select>
                            @error('upazila_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-bold text-slate-700 mb-2">জন্ম তারিখ</label>
                        <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d') ?? $user->date_of_birth) }}"
                               class="w-full rounded-xl border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-500 font-semibold text-slate-800 px-4 py-3">
                        @error('date_of_birth') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
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

                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl text-sm font-extrabold transition shadow-sm">
                        সেভ করুন
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-extrabold text-emerald-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            প্রোফাইল আপডেট হয়েছে!
                        </p>
                    @endif
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

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-xl text-sm font-extrabold transition shadow-sm">
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

    </div>
</div>

{{-- ⚙️ AJAX Script for Dynamic Location Pre-selection --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const divSelect = document.getElementById('division_id');
        const distSelect = document.getElementById('district_id');
        const upzSelect = document.getElementById('upazila_id');

        const savedDiv = "{{ auth()->user()->division_id }}";
        const savedDist = "{{ auth()->user()->district_id }}";
        const savedUpz = "{{ auth()->user()->upazila_id }}";

        // ১. বিভাগ লোড করা
        fetch('/ajax/divisions')
            .then(res => res.json())
            .then(data => {
                data.forEach(div => {
                    const selected = (div.id == savedDiv) ? 'selected' : '';
                    divSelect.innerHTML += `<option value="${div.id}" ${selected}>${div.name}</option>`;
                });
                if(savedDiv) divSelect.dispatchEvent(new Event('change'));
            });

        // ২. জেলা লোড করা
        divSelect.addEventListener('change', function() {
            const divId = this.value;
            distSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            distSelect.disabled = true;
            upzSelect.innerHTML = '<option value="">প্রথমে জেলা নির্বাচন করুন</option>';
            upzSelect.disabled = true;

            if (divId) {
                fetch(`/ajax/districts/${divId}`)
                    .then(res => res.json())
                    .then(data => {
                        distSelect.innerHTML = '<option value="">জেলা নির্বাচন করুন</option>';
                        distSelect.disabled = false;
                        data.forEach(dist => {
                            const selected = (dist.id == savedDist) ? 'selected' : '';
                            distSelect.innerHTML += `<option value="${dist.id}" ${selected}>${dist.name}</option>`;
                        });
                        if(savedDist) distSelect.dispatchEvent(new Event('change'));
                    });
            }
        });

        // ৩. উপজেলা লোড করা
        distSelect.addEventListener('change', function() {
            const distId = this.value;
            upzSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
            upzSelect.disabled = true;

            if (distId) {
                fetch(`/ajax/upazilas/${distId}`)
                    .then(res => res.json())
                    .then(data => {
                        upzSelect.innerHTML = '<option value="">থানা/উপজেলা নির্বাচন করুন</option>';
                        upzSelect.disabled = false;
                        data.forEach(upz => {
                            const selected = (upz.id == savedUpz) ? 'selected' : '';
                            upzSelect.innerHTML += `<option value="${upz.id}" ${selected}>${upz.name}</option>`;
                        });
                    });
            }
        });
    });
</script>
@endsection