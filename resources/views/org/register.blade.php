@extends('layouts.app')

@section('title', 'অর্গানাইজেশন/ক্লাব রেজিস্ট্রেশন — রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4">
    
    <div class="text-center mb-10">
        <span class="bg-red-50 text-red-600 font-extrabold px-4 py-1.5 rounded-full text-sm tracking-widest uppercase">পার্টনার প্রোগ্রাম</span>
        <h1 class="text-3xl md:text-4xl font-black text-slate-900 mt-4">আপনার অর্গানাইজেশন যুক্ত করুন</h1>
        <p class="text-slate-500 font-medium mt-3 max-w-2xl mx-auto">রক্তদূত নেটওয়ার্কে যুক্ত হয়ে আপনার ব্লাড ব্যাংক, হাসপাতাল বা স্বেচ্ছাসেবী সংগঠনের কার্যক্রমকে আরও প্রসারিত করুন।</p>
    </div>

    {{-- 🚀 Error Message Design Update --}}
    @if(session('error'))
        <div class="mb-8 p-5 bg-red-50 border border-red-200 text-red-700 font-bold rounded-xl flex items-start gap-3 shadow-sm">
            <svg class="w-6 h-6 shrink-0 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h3 class="text-lg font-black">রেজিস্ট্রেশন ব্যর্থ হয়েছে</h3>
                <p class="text-sm font-medium mt-1">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('org.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- 🏢 ধাপ ১: প্রাথমিক তথ্য --}}
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-xl font-extrabold text-slate-900 mb-6 border-b border-slate-100 pb-3">১. প্রাথমিক তথ্য</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">প্রতিষ্ঠানের ধরণ <span class="text-red-500">*</span></label>
                    <select name="org_type" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                        <option value="">নির্বাচন করুন</option>
                        <option value="hospital" @selected(old('org_type') == 'hospital')>হাসপাতাল (Hospital)</option>
                        <option value="blood_bank" @selected(old('org_type') == 'blood_bank')>ব্লাড ব্যাংক (Blood Bank)</option>
                        <option value="university_club" @selected(old('org_type') == 'university_club')>বিশ্ববিদ্যালয় ক্লাব (University Club)</option>
                        <option value="ngo" @selected(old('org_type') == 'ngo')>এনজিও (NGO)</option>
                        <option value="voluntary" @selected(old('org_type') == 'voluntary')>স্বেচ্ছাসেবী সংগঠন (Voluntary Org)</option>
                    </select>
                    @error('org_type') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">প্রতিষ্ঠানের পুরো নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="org_name" value="{{ old('org_name') }}" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                    @error('org_name') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">সংক্ষিপ্ত নাম (Acronym)</label>
                    <input type="text" name="short_name" value="{{ old('short_name') }}" placeholder="যেমন: DIU VBD" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">প্রতিষ্ঠার সাল</label>
                    <input type="number" name="established_year" value="{{ old('established_year') }}" placeholder="যেমন: 2015" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500">
                </div>
            </div>
        </div>

        {{-- 📞 ধাপ ২: যোগাযোগের তথ্য --}}
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-xl font-extrabold text-slate-900 mb-6 border-b border-slate-100 pb-3">২. যোগাযোগের তথ্য</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">অফিসিয়াল ইমেইল (লগইন আইডি) <span class="text-red-500">*</span></label>
                    <input type="email" name="official_email" value="{{ old('official_email') }}" placeholder="নতুন ইমেইল দিন" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                    @error('official_email') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">পাবলিক কন্টাক্ট নম্বর <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_number" value="{{ old('contact_number') }}" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                </div>
            </div>

            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 mb-6">
                <x-location-selector 
                    :selected-division="old('division_id')"
                    :selected-district="old('district_id')"
                    :selected-upazila="old('upazila_id')"
                />
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">বিস্তারিত ঠিকানা <span class="text-red-500">*</span></label>
                <textarea name="address_details" rows="2" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>{{ old('address_details') }}</textarea>
            </div>
        </div>

        {{-- 👤 ধাপ ৩: অ্যাডমিন ইনফো --}}
        <div class="bg-white p-6 md:p-8 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-xl font-extrabold text-slate-900 mb-6 border-b border-slate-100 pb-3">৩. অথোরাইজড পারসন (অ্যাডমিন)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">প্রতিনিধির নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">পদবি (Designation) <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_designation" value="{{ old('admin_designation') }}" placeholder="যেমন: General Secretary" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">ব্যক্তিগত ফোন নম্বর (গোপনীয়) <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_phone" value="{{ old('admin_phone') }}" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                    @error('admin_phone') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                    @error('password') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">কনফার্ম পাসওয়ার্ড <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="w-full rounded-xl border-slate-300 focus:border-red-500 focus:ring-red-500" required>
                </div>
            </div>
        </div>

        {{-- 📄 ধাপ ৪: ভেরিফিকেশন ডকুমেন্টস --}}
        <div class="bg-red-50 p-6 md:p-8 rounded-3xl border border-red-100 shadow-sm">
            <h2 class="text-xl font-extrabold text-red-900 mb-6 border-b border-red-200 pb-3">৪. ভেরিফিকেশন ডকুমেন্টস</h2>
            <p class="text-sm font-semibold text-red-700 mb-6">প্ল্যাটফর্মের নিরাপত্তা বজায় রাখতে সঠিক ডকুমেন্ট আপলোড করা বাধ্যতামূলক।</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-900 mb-2">অফিসিয়াল ডকুমেন্ট (PDF/Image) <span class="text-red-500">*</span></label>
                    <input type="file" name="official_document" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-extrabold file:bg-red-600 file:text-white hover:file:bg-red-700 transition" required>
                    <p class="text-xs text-slate-500 mt-2 font-medium">ট্রেড লাইসেন্স বা অনুমতিপত্র (সর্বোচ্চ ৫ মেগাবাইট)</p>
                    @error('official_document') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-900 mb-2">প্রতিষ্ঠানের লোগো (ঐচ্ছিক)</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-extrabold file:bg-slate-800 file:text-white hover:file:bg-slate-900 transition">
                    <p class="text-xs text-slate-500 mt-2 font-medium">PNG বা JPG ফরম্যাট (সর্বোচ্চ ২ মেগাবাইট)</p>
                </div>
            </div>
        </div>

        {{-- 🚀 Button UI Update --}}
        <div class="flex justify-center pt-8 mb-10">
            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-12 py-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-extrabold text-lg shadow-sm transition-all focus:ring-4 focus:ring-red-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                রেজিস্ট্রেশন সাবমিট করুন
            </button>
        </div>
    </form>
</div>
@endsection