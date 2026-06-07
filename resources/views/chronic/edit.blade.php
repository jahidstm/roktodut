@extends('layouts.app')

@section('title', 'এডিট সাবস্ক্রিপশন — রক্তদূত')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <a href="{{ route('chronic.show', $subscription->id) }}" class="inline-flex items-center gap-1 text-sm font-bold text-slate-500 hover:text-slate-800 mb-4 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            ফিরে যান
        </a>
        <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            সাবস্ক্রিপশন এডিট করুন
        </h1>
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

    <form action="{{ route('chronic.update', $subscription->id) }}" method="POST" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        
        {{-- Section 1: রোগীর বিবরণ --}}
        <div class="p-6 sm:p-8 border-b border-slate-100">
            <h2 class="text-lg font-black text-slate-800 mb-4">রোগীর বিবরণ</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রোগীর নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="patient_name" value="{{ old('patient_name', $subscription->patient_name) }}" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রোগের ধরন <span class="text-red-500">*</span></label>
                    <select name="condition_type" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        <option value="thalassemia" {{ old('condition_type', $subscription->condition_type) == 'thalassemia' ? 'selected' : '' }}>থ্যালাসেমিয়া (Thalassemia)</option>
                        <option value="dialysis" {{ old('condition_type', $subscription->condition_type) == 'dialysis' ? 'selected' : '' }}>ডায়ালাইসিস (Dialysis)</option>
                        <option value="sickle_cell" {{ old('condition_type', $subscription->condition_type) == 'sickle_cell' ? 'selected' : '' }}>সিকেল সেল (Sickle Cell)</option>
                        <option value="cancer" {{ old('condition_type', $subscription->condition_type) == 'cancer' ? 'selected' : '' }}>ক্যান্সার (Cancer)</option>
                        <option value="other" {{ old('condition_type', $subscription->condition_type) == 'other' ? 'selected' : '' }}>অন্যান্য</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                    @php $bgValue = $subscription->blood_group instanceof \App\Enums\BloodGroup ? $subscription->blood_group->value : $subscription->blood_group; @endphp
                    <select name="blood_group" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm font-bold text-red-600">
                        <option value="">নির্বাচন করুন</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $bgValue) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তের উপাদান <span class="text-red-500">*</span></label>
                    @php $compValue = $subscription->component_type instanceof \App\Enums\BloodComponentType ? $subscription->component_type->value : $subscription->component_type; @endphp
                    <select name="component_type" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        @foreach(\App\Enums\BloodComponentType::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('component_type', $compValue) == $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">কয় ব্যাগ প্রয়োজন (প্রতিবার) <span class="text-red-500">*</span></label>
                    <select name="bags_needed" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        @for($i=1; $i<=5; $i++)
                            <option value="{{ $i }}" {{ old('bags_needed', $subscription->bags_needed) == $i ? 'selected' : '' }}>{{ $i }} ব্যাগ</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">জরুরি অবস্থা <span class="text-red-500">*</span></label>
                    @php $urgencyValue = $subscription->urgency instanceof \App\Enums\UrgencyLevel ? $subscription->urgency->value : $subscription->urgency; @endphp
                    <select name="urgency" required class="w-full border-slate-200 rounded-xl focus:ring-red-500 focus:border-red-500 shadow-sm">
                        <option value="normal" {{ old('urgency', $urgencyValue) == 'normal' ? 'selected' : '' }}>স্বাভাবিক (Normal)</option>
                        <option value="urgent" {{ old('urgency', $urgencyValue) == 'urgent' ? 'selected' : '' }}>জরুরি (Urgent)</option>
                        <option value="emergency" {{ old('urgency', $urgencyValue) == 'emergency' ? 'selected' : '' }}>মুমূর্ষু (Emergency)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 2: শিডিউল --}}
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-lg font-black text-slate-800 mb-4">রক্তদানের শিডিউল</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">পরবর্তী কবে রক্ত লাগবে? <span class="text-red-500">*</span></label>
                    <input type="date" name="next_needed_at" value="{{ old('next_needed_at', $subscription->next_needed_at?->format('Y-m-d')) }}" required class="w-full border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">কত দিন পরপর লাগবে? <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="cadence_days" value="{{ old('cadence_days', $subscription->cadence_days) }}" min="14" max="90" required class="w-full border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm pr-12">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-sm font-bold text-slate-400">দিন</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">কত দিন আগে ডোনার খুঁজবে? <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $subscription->lead_time_days) }}" min="1" max="7" required class="w-full border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 shadow-sm pr-12">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-sm font-bold text-slate-400">দিন</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: ঠিকানা ও যোগাযোগ --}}
        <div class="p-6 sm:p-8 border-b border-slate-100">
            <h2 class="text-lg font-black text-slate-800 mb-4">হাসপাতাল ও যোগাযোগ</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">বিভাগ <span class="text-red-500">*</span></label>
                    <select name="division_id" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $subscription->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">জেলা <span class="text-red-500">*</span></label>
                    <select name="district_id" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id', $subscription->district_id) == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">উপজেলা/থানা <span class="text-red-500">*</span></label>
                    <input type="number" name="upazila_id" value="{{ old('upazila_id', $subscription->upazila_id) }}" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">হাসপাতাল (ঐচ্ছিক)</label>
                    <select name="hospital_id" class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                        <option value="">নির্বাচন করুন</option>
                        @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}" {{ old('hospital_id', $subscription->hospital_id) == $hospital->id ? 'selected' : '' }}>{{ $hospital->display_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-1.5">বিস্তারিত ঠিকানা</label>
                <input type="text" name="address" value="{{ old('address', $subscription->address) }}" class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">যোগাযোগের নম্বর <span class="text-red-500">*</span></label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $subscription->contact_number) }}" required class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm font-semibold">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">যোগাযোগকারীর নাম (ঐচ্ছিক)</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $subscription->contact_name) }}" class="w-full border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 shadow-sm">
                </div>
            </div>
            
            <div class="mt-4 flex items-center gap-2">
                <input type="hidden" name="is_phone_hidden" value="0">
                <input type="checkbox" name="is_phone_hidden" id="is_phone_hidden" value="1" {{ old('is_phone_hidden', $subscription->is_phone_hidden) ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                <label for="is_phone_hidden" class="text-sm font-semibold text-slate-700">আমার ফোন নম্বর পাবলিকলি হাইড রাখুন</label>
            </div>
        </div>

        {{-- Section 4: বিশেষ নির্দেশনা --}}
        <div class="p-6 sm:p-8">
            <h2 class="text-lg font-black text-slate-800 mb-4">বিশেষ নির্দেশনা</h2>
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-1.5">পাবলিক নোট</label>
                <textarea name="notes" rows="2" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('notes', $subscription->notes) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">ব্লাড বাডিদের জন্য নোট</label>
                <textarea name="notes_for_donor" rows="2" class="w-full border-slate-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 shadow-sm">{{ old('notes_for_donor', $subscription->notes_for_donor) }}</textarea>
            </div>
        </div>

        {{-- Submit --}}
        <div class="bg-slate-50 px-6 py-5 border-t border-slate-100 flex justify-end">
            <button type="submit" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white font-black py-3 px-8 rounded-xl shadow-lg transition">
                সেভ করুন
            </button>
        </div>
    </form>
</div>
@endsection
