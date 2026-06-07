@extends('layouts.app')

@section('title', 'নতুন সাবস্ক্রিপশন — রক্তদূত')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <a href="{{ route('chronic.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 hover:text-slate-800 mb-4 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            ফিরে যান
        </a>
        <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন দীর্ঘমেয়াদী সাবস্ক্রিপশন
        </h1>
        <p class="text-slate-500 font-medium text-sm mt-1">রোগীর তথ্য এবং শিডিউল সেট করুন। সিস্টেম স্বয়ংক্রিয়ভাবে রিকোয়েস্ট তৈরি করে ডোনার খুঁজবে।</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h3 class="text-sm font-bold text-red-800 mb-1">কিছু তথ্য ভুল হয়েছে:</h3>
                    <ul class="list-disc list-inside text-sm font-medium text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('chronic.store') }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        
        {{-- Section 1: রোগীর বিবরণ --}}
        <div class="p-6 sm:p-8 border-b border-slate-100">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs">১</span>
                রোগীর বিবরণ
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রোগীর নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="patient_name" value="{{ old('patient_name') }}" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রোগের ধরন <span class="text-red-500">*</span></label>
                    <select name="condition_type" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        <option value="thalassemia" {{ old('condition_type') == 'thalassemia' ? 'selected' : '' }}>থ্যালাসেমিয়া (Thalassemia)</option>
                        <option value="dialysis" {{ old('condition_type') == 'dialysis' ? 'selected' : '' }}>ডায়ালাইসিস (Dialysis)</option>
                        <option value="sickle_cell" {{ old('condition_type') == 'sickle_cell' ? 'selected' : '' }}>সিকেল সেল (Sickle Cell)</option>
                        <option value="cancer" {{ old('condition_type') == 'cancer' ? 'selected' : '' }}>ক্যান্সার (Cancer)</option>
                        <option value="other" {{ old('condition_type') == 'other' ? 'selected' : '' }}>অন্যান্য</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                    <select name="blood_group" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm font-bold text-red-600">
                        <option value="">নির্বাচন করুন</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তের উপাদান <span class="text-red-500">*</span></label>
                    <select name="component_type" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        @foreach(\App\Enums\BloodComponentType::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('component_type') == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">কয় ব্যাগ প্রয়োজন (প্রতিবার) <span class="text-red-500">*</span></label>
                    <select name="bags_needed" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}" {{ old('bags_needed') == $i ? 'selected' : '' }}>{{ $i }} ব্যাগ</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">জরুরি অবস্থা (Urgency) <span class="text-red-500">*</span></label>
                    <select name="urgency" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        <option value="normal" {{ old('urgency') == 'normal' ? 'selected' : '' }}>স্বাভাবিক (Normal)</option>
                        <option value="urgent" {{ old('urgency') == 'urgent' ? 'selected' : '' }}>জরুরি (Urgent)</option>
                        <option value="emergency" {{ old('urgency') == 'emergency' ? 'selected' : '' }}>মুমূর্ষু (Emergency)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 2: শিডিউল --}}
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs">২</span>
                রক্তদানের শিডিউল
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-start">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">পরবর্তী কবে রক্ত লাগবে? <span class="text-red-500">*</span></label>
                    <input type="date" name="next_needed_at" value="{{ old('next_needed_at') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required class="w-full border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">কত দিন পরপর লাগবে? <span class="text-red-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl">
                        <input type="number" name="cadence_days" value="{{ old('cadence_days', 30) }}" min="14" max="90" required class="w-full border-slate-200 rounded-l-xl focus:ring-blue-500 focus:border-blue-500 border-r-0 focus:z-10">
                        <span class="inline-flex items-center px-4 rounded-r-xl border border-l-0 border-slate-200 bg-slate-50 text-slate-500 text-sm font-bold">
                            দিন
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">কত দিন আগে ডোনার খুঁজবে? <span class="text-red-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl">
                        <input type="number" name="lead_time_days" value="{{ old('lead_time_days', 3) }}" min="1" max="7" required class="w-full border-slate-200 rounded-l-xl focus:ring-blue-500 focus:border-blue-500 border-r-0 focus:z-10">
                        <span class="inline-flex items-center px-4 rounded-r-xl border border-l-0 border-slate-200 bg-slate-50 text-slate-500 text-sm font-bold">
                            দিন
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1.5">রক্ত লাগার কত দিন আগে অটো-রিকোয়েস্ট তৈরি হবে।</p>
                </div>
            </div>
        </div>

        {{-- Section 3: ঠিকানা ও যোগাযোগ --}}
        <div class="p-6 sm:p-8 border-b border-slate-100">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">৩</span>
                হাসপাতাল ও যোগাযোগ
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <!-- Location selects (simplified for brevity, use alpine/js for cascading) -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">বিভাগ <span class="text-red-500">*</span></label>
                    <select name="division_id" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">জেলা <span class="text-red-500">*</span></label>
                    <select name="district_id" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id', 47) == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">উপজেলা/থানা <span class="text-red-500">*</span></label>
                    <input type="number" name="upazila_id" value="{{ old('upazila_id', 1) }}" placeholder="Upazila ID" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">হাসপাতাল (ঐচ্ছিক)</label>
                    <select name="hospital_id" class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}" {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>{{ $hospital->display_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-1.5">বিস্তারিত ঠিকানা</label>
                <input type="text" name="address" value="{{ old('address') }}" placeholder="হাসপাতালের ওয়ার্ড/কেবিন নম্বর" class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">যোগাযোগের নম্বর <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', auth()->user()->phone) }}" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm font-semibold">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">যোগাযোগকারীর নাম (ঐচ্ছিক)</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                </div>
            </div>
            
            <div class="mt-4 flex items-center gap-2">
                <input type="hidden" name="is_phone_hidden" value="0">
                <input type="checkbox" name="is_phone_hidden" id="is_phone_hidden" value="1" {{ old('is_phone_hidden') ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                <label for="is_phone_hidden" class="text-sm font-semibold text-slate-700">আমার ফোন নম্বর পাবলিকলি হাইড রাখুন (শুধু ডোনার দেখবে)</label>
            </div>
        </div>

        {{-- Section 4: বিশেষ নির্দেশনা --}}
        <div class="p-6 sm:p-8">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs">৪</span>
                বিশেষ নির্দেশনা
            </h2>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-1.5">পাবলিক নোট (সবাই দেখবে)</label>
                <textarea name="notes" rows="2" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 shadow-sm" placeholder="যেমন: রোগীর অবস্থা ক্রিটিকাল...">{{ old('notes') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">ব্লাড বাডিদের জন্য নোট (ঐচ্ছিক)</label>
                <textarea name="notes_for_donor" rows="2" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 shadow-sm" placeholder="শুধুমাত্র আপনার নির্ধারিত ডোনাররা (বাডি) এটি দেখতে পাবেন। যেমন: ডোনারকে অবশ্যই সকালে খালি পেটে আসতে হবে...">{{ old('notes_for_donor') }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <div class="bg-slate-50 px-6 py-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs font-semibold text-slate-500">
                <span class="text-red-500">*</span> চিহ্নিত ঘরগুলো পূরণ করা আবশ্যক
            </p>
            <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-black py-3 px-8 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                সাবস্ক্রিপশন চালু করুন
            </button>
        </div>
    </form>
</div>
@endsection
