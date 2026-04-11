<x-auth-split-layout maxWidth="max-w-[40rem]">
    @section('title', 'অ্যাকাউন্ট সেটআপ')
    
    <div class="mb-8 text-center sm:px-6">
        <h2 class="text-3xl font-extrabold text-slate-800 mb-2">স্বাগতম, {{ auth()->user() ? auth()->user()->name : 'ব্যবহারকারী' }}!</h2>
        <p class="text-slate-500 font-medium leading-relaxed">আপনার প্রোফাইলটি সম্পূর্ণ করতে নিচের তথ্যগুলো প্রদান করুন।</p>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6">
        @csrf

        <!-- Location Section -->
        <div class="p-6 border border-slate-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow">
            <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 flex items-center">
                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                লোকেশন তথ্য <span class="text-red-500 ml-1">*</span>
            </h3>
            
            <x-location-selector 
                :selected-division="old('division_id')"
                :selected-district="old('district_id')"
                :selected-upazila="old('upazila_id')"
            />
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-1">
                <div>@error('division_id') <div class="text-sm text-red-600 font-semibold">{{ $message }}</div> @enderror</div>
                <div>@error('district_id') <div class="text-sm text-red-600 font-semibold">{{ $message }}</div> @enderror</div>
                <div>@error('upazila_id') <div class="text-sm text-red-600 font-semibold">{{ $message }}</div> @enderror</div>
            </div>
        </div>

        <!-- Personal & Donor Info Section -->
        <div class="p-6 border border-slate-200 rounded-xl bg-white shadow-sm hover:shadow-md transition-shadow space-y-5">
            <h3 class="text-sm font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2 flex items-center">
                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                ব্যক্তিগত ও ডোনার তথ্য
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="gender" class="block text-sm font-semibold text-slate-700 mb-1.5">লিঙ্গ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="gender" name="gender" class="input-modern appearance-none bg-none bg-white cursor-pointer pr-10" required>
                            <option value="">নির্বাচন করুন</option>
                            <option value="male" @selected(old('gender') == 'male')>পুরুষ</option>
                            <option value="female" @selected(old('gender') == 'female')>মহিলা</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    @error('gender') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label for="weight" class="block text-sm font-semibold text-slate-700 mb-1.5">ওজন (কেজি)</label>
                    <input id="weight" type="number" name="weight" value="{{ old('weight') }}" placeholder="যেমন: 65" class="input-modern bg-white" />
                    @error('weight') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-1">
                <div>
                    <label for="last_donation_date" class="block text-sm font-semibold text-slate-700 mb-1.5">শেষ রক্তদানের তারিখ (ঐচ্ছিক)</label>
                    <input id="last_donation_date" type="date" name="last_donation_date" value="{{ old('last_donation_date') }}" max="{{ date('Y-m-d') }}" class="input-modern bg-white text-slate-700 cursor-pointer" />
                    <p class="text-xs text-slate-500 mt-2 font-medium">কখনো রক্ত না দিয়ে থাকলে ফাঁকা রাখুন।</p>
                    @error('last_donation_date') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>

                <!-- Organization Selection -->
                <div>
                    <label for="organization_id" class="block text-sm font-semibold text-slate-700 mb-1.5">অর্গানাইজেশন/ক্লাব (ঐচ্ছিক)</label>
                    <div class="relative">
                        <select id="organization_id" name="organization_id" class="input-modern appearance-none bg-none bg-white cursor-pointer pr-10">
                            <option value="">কোনো ক্লাবের সাথে যুক্ত নই</option>
                            @if(isset($organizations))
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}" @selected(old('organization_id') == $org->id)>{{ $org->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 font-medium leading-relaxed">মেম্বার হয়ে থাকলে নির্বাচন করুন। ভেরিফাই হলে ব্লু-ব্যাজ পাবেন।</p>
                    @error('organization_id') <div class="text-sm text-red-600 font-semibold mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 pb-2">
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all hover:shadow-red-600/40 hover:-translate-y-1 text-lg">
                প্রোফাইল সেভ করুন ও ড্যাশবোর্ডে যান
            </button>
        </div>
    </form>
</x-auth-split-layout>