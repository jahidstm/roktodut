@extends('layouts.donor-dashboard')

@section('title', 'অফলাইন ক্লেইম — রক্তদূত')
@section('page-title', 'অফলাইন ক্লেইম')

@section('content')
<div class="flex flex-col gap-6">
    <section class="rounded-3xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('offline-claims.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">অফলাইন রক্তদান ক্লেইম</h2>
                <p class="text-sm font-medium text-slate-500 mt-1">রিকোয়েস্ট ছাড়া ফোনে রক্ত দিলে এখানে ক্লেইম দিন।</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">গ্রহীতার ফোন <span class="text-red-500">*</span></label>
                    <input type="text" name="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="01XXXXXXXXX" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                    @error('recipient_phone') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রোগীর নাম <span class="text-red-500">*</span></label>
                    <input type="text" name="patient_name" value="{{ old('patient_name') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                    @error('patient_name') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">জেলা <span class="text-red-500">*</span></label>
                    <select name="district_id" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                        <option value="">জেলা নির্বাচন করুন</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" @selected((int) old('district_id') === (int) $district->id)>{{ $district->name }}</option>
                        @endforeach
                    </select>
                    @error('district_id') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">রক্তদানের তারিখ <span class="text-red-500">*</span></label>
                    <input type="date" name="donation_date" value="{{ old('donation_date') }}" max="{{ now()->toDateString() }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500" required>
                    @error('donation_date') <p class="text-xs font-bold text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">হাসপাতালের নাম (ঐচ্ছিক)</label>
                <input type="text" name="hospital_name" value="{{ old('hospital_name') }}" class="w-full rounded-xl border-slate-300 text-sm font-semibold focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">প্রুফ ছবি (ঐচ্ছিক)</label>
                <input type="file" name="proof_path" accept="image/*" class="w-full rounded-xl border border-slate-300 bg-white text-sm font-semibold file:mr-4 file:border-0 file:bg-red-600 file:px-4 file:py-2.5 file:text-white hover:file:bg-red-700">
                <p class="text-xs text-slate-500 font-medium mt-1">ছবি না দিলে অ্যাডমিন রিভিউতে যাবে।</p>
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <a href="{{ route('donor.dashboard') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">বাতিল</a>
                <button type="submit" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700">ক্লেইম সাবমিট</button>
            </div>
        </form>
    </section>
</div>
@endsection
