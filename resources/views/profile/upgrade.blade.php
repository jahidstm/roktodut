@php
    $layout = 'layouts.app';
    if(auth()->check()){
        $u = auth()->user();
        if($u->isAdmin()) {
            $layout = 'layouts.admin-dashboard';
        } elseif($u->isOrgAdmin()) {
            $layout = 'layouts.org-dashboard';
        } elseif($u->isDonor()) {
            $layout = 'layouts.donor-dashboard';
        } else {
            $layout = 'layouts.user-dashboard';
        }
    }
@endphp
@extends($layout)

@section('title', 'রক্তদাতা হিসেবে যুক্ত হন — রক্তদূত')

@section('content')
<div class="bg-[#f8fafc] min-h-screen py-10 px-4 sm:px-6 lg:px-8 relative overflow-hidden" data-panel-id="upgrade">
    <!-- Abstract Background Decorators -->
    <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-red-50/80 to-transparent pointer-events-none"></div>
    
    <div class="max-w-2xl mx-auto space-y-10 relative z-10">

        {{-- পেজ হেডার --}}
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left mb-6 scroll-reveal" data-scroll-reveal>
            <div class="w-16 h-16 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 flex items-center justify-center shrink-0 relative">
                <div class="absolute inset-0 bg-red-500/10 rounded-2xl rotate-3 scale-105 -z-10"></div>
                <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">রক্তদাতা হিসেবে যুক্ত হন</h1>
                <p class="mt-2 text-slate-500 font-medium">আপনার প্রোফাইল আপগ্রেড করুন এবং মানুষের জীবন বাঁচাতে অবদান রাখুন।</p>
            </div>
        </div>

        {{-- Upgrade Form --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200 scroll-reveal" data-scroll-reveal>
            <form method="POST" action="{{ route('profile.upgrade_to_donor') }}" class="space-y-6">
                @csrf
                @if(empty($user->phone))
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">ফোন নম্বর <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required placeholder="01XXX-XXXXXX">
                    @error('phone') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                        <select name="blood_group" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" @selected(old('blood_group', $user->blood_group?->value ?? $user->blood_group) == $bg)>{{ $bg }}</option>
                            @endforeach
                        </select>
                        @error('blood_group') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">লিঙ্গ <span class="text-red-500">*</span></label>
                        <select name="gender" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                            <option value="">নির্বাচন করুন</option>
                            <option value="male" @selected(old('gender', $user->gender) == 'male')>পুরুষ</option>
                            <option value="female" @selected(old('gender', $user->gender) == 'female')>মহিলা</option>
                        </select>
                        @error('gender') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">লোকেশন <span class="text-red-500">*</span></label>
                    <x-location-selector
                        :selected-division="old('division_id', $user->division_id)"
                        :selected-district="old('district_id', $user->district_id)"
                        :selected-upazila="old('upazila_id', $user->upazila_id)"
                    />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">ওজন (কেজি)</label>
                        <input type="number" name="weight" value="{{ old('weight', $user->weight) }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" placeholder="যেমন: 65">
                        @error('weight') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">শেষ রক্তদানের তারিখ</label>
                        <input type="date" name="last_donation_date" value="{{ old('last_donation_date', $user->last_donated_at?->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500 text-slate-700">
                        @error('last_donation_date') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="pt-4 flex flex-col sm:flex-row justify-between items-center border-t border-slate-100 mt-6 gap-3">
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center px-6 py-3 text-slate-500 hover:text-slate-700 font-bold transition">
                        ফিরে যান
                    </a>
                    <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-xl font-black shadow-md shadow-red-200 transition-all">
                        আপগ্রেড নিশ্চিত করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
