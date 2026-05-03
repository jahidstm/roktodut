@extends('layouts.app')

@section('title', 'নতুন রক্তের রিকোয়েস্ট — রক্তদূত')

@section('content')
{{-- 🎯 THE FIX: ফর্মটি মাঝখানে রাখতে এবং উপরে-নিচে পর্যাপ্ত শ্বাস নেওয়ার জায়গা (Breathing Space) দিতে px-4 sm:px-6 lg:px-8 py-10 যোগ করা হলো --}}
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @include('partials.pilot-banner')

    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">নতুন রক্তের রিকোয়েস্ট</h1>
    <p class="text-slate-500 font-medium mt-1">সঠিক তথ্য দিলে দ্রুত ডোনার রেসপন্স পাবে।</p>

    @if(session('existing_request_url'))
        <div class="mt-4">
            <a href="{{ session('existing_request_url') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-extrabold text-amber-700 hover:bg-amber-100 transition">
                আগের অনুরোধ দেখুন
            </a>
        </div>
    @endif

    <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 w-2 h-full bg-red-600"></div>
        
        {{-- 🎯 ফর্মের সাথে Alpine.js x-data যোগ করা হলো --}}
        <form method="POST" action="{{ route('requests.store') }}" class="space-y-6" x-data="{ isSubmitting: false }" @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">রোগীর নাম</label>
                    <input name="patient_name" value="{{ old('patient_name') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                    @error('patient_name') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                    <select name="blood_group" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                            <option value="{{ $group }}" @selected(old('blood_group') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                    @error('blood_group') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">হাসপাতাল</label>
                    <input name="hospital" value="{{ old('hospital') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                    @error('hospital') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">ব্যাগ প্রয়োজন <span class="text-red-500">*</span></label>
                    <input type="number" min="1" name="bags_needed" value="{{ old('bags_needed', 1) }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3" />
                    @error('bags_needed') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 📍 Dynamic Location Dropdowns --}}
            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <x-location-selector 
                    :selected-division="old('division_id')"
                    :selected-district="old('district_id')"
                    :selected-upazila="old('upazila_id')"
                />
                
                {{-- Error states for location --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-3">
                    @error('division_id') <div class="text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                    @error('district_id') <div class="text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                    @error('upazila_id') <div class="text-sm text-red-600 font-bold">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-800">ঠিকানা</label>
                <input name="address" value="{{ old('address') }}" placeholder="যেমন: ওয়ার্ড নং ৩, সদর হাসপাতাল রোড"
                       class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                @error('address') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">কন্টাক্ট নাম</label>
                    <input name="contact_name" value="{{ old('contact_name') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3" />
                    @error('contact_name') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">কন্টাক্ট নাম্বার <span class="text-red-500">*</span></label>
                    <input name="contact_number" value="{{ old('contact_number') }}" placeholder="01XXXXXXXXX"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3" />
                    @error('contact_number') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">জরুরিতা <span class="text-red-500">*</span></label>
                    <select id="urgency" name="urgency" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3">
                        <option value="">সিলেক্ট করুন</option>
                        @foreach (\App\Enums\UrgencyLevel::cases() as $case)
                            <option value="{{ $case->value }}" @selected(old('urgency') === $case->value)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                    @error('urgency') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                    <div id="urgency-threshold-note" class="mt-1 text-xs font-semibold text-amber-700 hidden"></div>
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">কবে রক্ত লাগবে <span class="text-red-500">*</span></label>
                    <input id="needed_at" type="datetime-local" name="needed_at" value="{{ old('needed_at') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-bold px-4 py-3" />
                    @error('needed_at') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-800">অতিরিক্ত নোট</label>
                <textarea name="notes" rows="3" placeholder="রোগীর বর্তমান অবস্থা বা অন্য কোনো তথ্য..."
                          class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium px-4 py-3">{{ old('notes') }}</textarea>
                @error('notes') <div class="text-sm text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
            </div>

            <x-captcha-field :captcha-question="$captchaQuestion" />

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                {{-- 🎯 ডাইনামিক সাবমিট বাটন --}}
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="bg-red-600 hover:bg-red-700 disabled:bg-slate-400 disabled:cursor-not-allowed text-white px-8 py-3.5 rounded-xl text-sm font-black transition-all shadow-sm shadow-red-200 flex items-center justify-center min-w-[200px]">
                    
                    {{-- নরমাল অবস্থা --}}
                    <span x-show="!isSubmitting" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        রিকোয়েস্ট সাবমিট করুন
                    </span>

                    {{-- লোডিং অবস্থা --}}
                    <span x-show="isSubmitting" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        প্রসেসিং হচ্ছে...
                    </span>
                </button>
                
                <a href="{{ route('requests.index') }}" class="px-6 py-3.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition">
                    বাতিল
                </a>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urgencySelect = document.getElementById('urgency');
    const neededAtInput = document.getElementById('needed_at');
    const note = document.getElementById('urgency-threshold-note');

    if (!urgencySelect || !neededAtInput || !note) {
        return;
    }

    const emergencyOption = urgencySelect.querySelector('option[value="emergency"]');
    const urgentOption = urgencySelect.querySelector('option[value="urgent"]');
    const normalOption = urgencySelect.querySelector('option[value="normal"]');

    const updateUrgencyAvailability = () => {
        const raw = neededAtInput.value;

        if (!raw) {
            if (emergencyOption) emergencyOption.disabled = false;
            if (urgentOption) urgentOption.disabled = false;
            note.classList.add('hidden');
            note.textContent = '';
            return;
        }

        const selectedDate = new Date(raw);
        if (Number.isNaN(selectedDate.getTime())) {
            return;
        }

        const now = new Date();
        const emergencyLimit = new Date(now.getTime() + (24 * 60 * 60 * 1000));
        const urgentLimit = new Date(now.getTime() + (72 * 60 * 60 * 1000));

        const disableEmergency = selectedDate > emergencyLimit;
        const disableUrgent = selectedDate > urgentLimit;

        if (emergencyOption) emergencyOption.disabled = disableEmergency;
        if (urgentOption) urgentOption.disabled = disableUrgent;

        if (urgencySelect.value === 'emergency' && disableEmergency) {
            urgencySelect.value = normalOption ? 'normal' : '';
        }

        if (urgencySelect.value === 'urgent' && disableUrgent) {
            urgencySelect.value = normalOption ? 'normal' : '';
        }

        if (disableUrgent) {
            note.textContent = 'নির্বাচিত সময় ৭২ ঘণ্টার বেশি — Emergency ও Urgent অপশন নিষ্ক্রিয়।';
            note.classList.remove('hidden');
            return;
        }

        if (disableEmergency) {
            note.textContent = 'নির্বাচিত সময় ২৪ ঘণ্টার বেশি — Emergency অপশন নিষ্ক্রিয়।';
            note.classList.remove('hidden');
            return;
        }

        note.classList.add('hidden');
        note.textContent = '';
    };

    neededAtInput.addEventListener('change', updateUrgencyAvailability);
    neededAtInput.addEventListener('input', updateUrgencyAvailability);
    updateUrgencyAvailability();
});
</script>
@endsection
