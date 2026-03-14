@extends('layouts.app')

@section('title', 'নতুন রক্তের রিকোয়েস্ট — রক্তদূত')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-extrabold tracking-tight">নতুন রক্তের রিকোয়েস্ট</h1>
    <p class="text-slate-500 font-medium mt-1">সঠিক তথ্য দিলে দ্রুত ডোনার রেসপন্স পাবে।</p>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('requests.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">রোগীর নাম</label>
                    <input name="patient_name" value="{{ old('patient_name') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('patient_name') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">রক্তের গ্রুপ *</label>
                    <select name="blood_group" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-200">
                        <option value="">সিলেক্ট করুন</option>
                        <option value="A+">A+</option><option value="A-">A-</option>
                        <option value="B+">B+</option><option value="B-">B-</option>
                        <option value="AB+">AB+</option><option value="AB-">AB-</option>
                        <option value="O+">O+</option><option value="O-">O-</option>
                    </select>
                    @error('blood_group') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">হাসপাতাল</label>
                    <input name="hospital" value="{{ old('hospital') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('hospital') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">ব্যাগ প্রয়োজন *</label>
                    <input type="number" min="1" name="bags_needed" value="{{ old('bags_needed', 1) }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('bags_needed') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">জেলা *</label>
                    <input name="district" value="{{ old('district') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('district') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">থানা *</label>
                    <input name="thana" value="{{ old('thana') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('thana') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-800">ঠিকানা</label>
                <input name="address" value="{{ old('address') }}"
                       class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                @error('address') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">কন্টাক্ট নাম</label>
                    <input name="contact_name" value="{{ old('contact_name') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('contact_name') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">কন্টাক্ট নাম্বার *</label>
                    <input name="contact_number" value="{{ old('contact_number') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('contact_number') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-extrabold text-slate-800">জরুরিতা *</label>
                    <select name="urgency" class="mt-2 w-full rounded-xl border-slate-200 bg-white focus:border-red-500 focus:ring-red-200">
                        <option value="">সিলেক্ট করুন</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    @error('urgency') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm font-extrabold text-slate-800">দরকার (needed_at) *</label>
                    <input type="datetime-local" name="needed_at" value="{{ old('needed_at') }}"
                           class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200" />
                    @error('needed_at') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-800">নোট</label>
                <textarea name="notes" rows="4"
                          class="mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-200">{{ old('notes') }}</textarea>
                @error('notes') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="flex items-center gap-3">
                <button class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-extrabold shadow-sm shadow-red-200">
                    সাবমিট
                </button>
                <a href="{{ route('requests.index') }}" class="font-extrabold text-slate-700 hover:text-red-600">
                    বাতিল
                </a>
            </div>
        </form>
    </div>
</div>
@endsection