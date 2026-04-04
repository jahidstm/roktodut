<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>অ্যাকাউন্ট সেটআপ — রক্তদূত</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>:root { font-family: 'Hind Siliguri', sans-serif; }</style>
</head>
<body class="bg-slate-50 antialiased min-h-screen flex items-center justify-center py-10 px-4">

    <div class="w-full max-w-3xl bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        
        {{-- 🎨 Header --}}
        <div class="bg-slate-900 px-6 py-8 text-center">
            <h2 class="font-black text-2xl text-white">স্বাগতম, {{ auth()->user()->name }}!</h2>
            <p class="mt-2 text-slate-300 font-medium">আপনার প্রোফাইলটি সম্পূর্ণ করতে নিচের তথ্যগুলো প্রদান করুন।</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="p-6 md:p-8 space-y-8">
            @csrf

            {{-- 📍 Location Section --}}
            <div class="p-6 border border-slate-200 rounded-2xl bg-slate-50 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-800 mb-4 border-b border-slate-200 pb-2">লোকেশন তথ্য <span class="text-red-500">*</span></h3>
                
                {{-- গ্লোবাল লোকেশন কম্পোনেন্ট --}}
                <x-location-selector 
                    :selected-division="old('division_id')"
                    :selected-district="old('district_id')"
                    :selected-upazila="old('upazila_id')"
                />
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    @error('division_id') <div class="text-xs text-red-600 font-bold">{{ $message }}</div> @enderror
                    @error('district_id') <div class="text-xs text-red-600 font-bold">{{ $message }}</div> @enderror
                    @error('upazila_id') <div class="text-xs text-red-600 font-bold">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- 🩸 Personal & Donor Info (সবার জন্য উন্মুক্ত করা হলো) --}}
            <div class="p-6 border border-red-100 rounded-2xl bg-red-50/30 shadow-sm space-y-6">
                <h3 class="text-sm font-extrabold text-red-800 mb-2 border-b border-red-100 pb-2">ব্যক্তিগত ও ডোনার তথ্য</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="gender" class="block text-sm font-extrabold text-slate-800">লিঙ্গ <span class="text-red-500">*</span></label>
                        <select id="gender" name="gender" class="block mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium" required>
                            <option value="">নির্বাচন করুন</option>
                            <option value="male" @selected(old('gender') == 'male')>পুরুষ</option>
                            <option value="female" @selected(old('gender') == 'female')>মহিলা</option>
                        </select>
                        @error('gender') <div class="text-xs text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-extrabold text-slate-800">ওজন (কেজি)</label>
                        <input id="weight" type="number" name="weight" value="{{ old('weight') }}" placeholder="যেমন: 65" class="block mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium" />
                        @error('weight') <div class="text-xs text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label for="last_donation_date" class="block text-sm font-extrabold text-slate-800">শেষ রক্তদানের তারিখ (ঐচ্ছিক)</label>
                    <input id="last_donation_date" type="date" name="last_donation_date" value="{{ old('last_donation_date') }}" max="{{ date('Y-m-d') }}" class="block mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium" />
                    <p class="text-xs text-gray-500 mt-1 font-medium">কখনো রক্ত না দিয়ে থাকলে ফাঁকা রাখুন।</p>
                    @error('last_donation_date') <div class="text-xs text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- 🏢 Organization Selection --}}
                <div>
                    <label for="organization_id" class="block text-sm font-extrabold text-slate-800">অর্গানাইজেশন/ক্লাব (ঐচ্ছিক)</label>
                    <select id="organization_id" name="organization_id" class="block mt-2 w-full rounded-xl border-slate-200 focus:border-red-500 focus:ring-red-500 font-medium">
                        <option value="">কোনো ব্লাড ক্লাবের সাথে যুক্ত নই</option>
                        @if(isset($organizations))
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" @selected(old('organization_id') == $org->id)>{{ $org->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p class="text-xs text-gray-500 mt-1 font-medium">আপনি কোনো ক্লাবের মেম্বার হয়ে থাকলে নির্বাচন করুন। অ্যাডমিন ভেরিফাই করলে আপনার প্রোফাইলে ব্লু-ব্যাজ যুক্ত হবে।</p>
                    @error('organization_id') <div class="text-xs text-red-600 font-bold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full flex justify-center bg-red-600 hover:bg-red-700 text-white py-4 rounded-xl text-lg font-black shadow-lg shadow-red-200 transition-all">
                    প্রোফাইল সেভ করুন ও ড্যাশবোর্ডে যান
                </button>
            </div>
        </form>
    </div>

</body>
</html>