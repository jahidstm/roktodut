<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-black text-2xl text-slate-900 leading-tight">
                নতুন অ্যাম্বুলেন্স যুক্ত করুন
            </h2>
            <a href="{{ route('ambulances.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700">
                &larr; ডিরেক্টরিতে ফিরে যান
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mb-8 flex gap-4 items-start">
                <div class="text-3xl">💡</div>
                <div>
                    <h4 class="font-black text-blue-900 mb-1">সঠিক তথ্য দিয়ে পয়েন্ট জিতুন!</h4>
                    <p class="text-sm text-blue-800 font-medium">
                        আপনার পরিচিত বা লোকাল হাসপাতালের কোনো অ্যাম্বুলেন্সের সঠিক তথ্য এখানে সাবমিট করুন। অ্যাডমিন প্যানেল থেকে আপনার দেওয়া তথ্য ভেরিফাই হওয়ার পর এটি পাবলিক ডিরেক্টরিতে যুক্ত হবে এবং আপনি স্পেশাল গ্যামিফিকেশন পয়েন্ট পাবেন!
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-slate-100 p-8 sm:p-10">
                <form action="{{ route('user.ambulances.store') }}" method="POST" x-data="{
                    districts: [],
                    upazilas: [],
                    loadingDistricts: false,
                    loadingUpazilas: false,
                    fetchDistricts(divisionId) {
                        this.districts = [];
                        this.upazilas = [];
                        if (!divisionId) return;
                        this.loadingDistricts = true;
                        fetch('/ajax/districts/' + divisionId)
                            .then(r => r.json())
                            .then(data => { this.districts = data; this.loadingDistricts = false; })
                            .catch(() => { this.loadingDistricts = false; });
                    },
                    fetchUpazilas(districtId) {
                        this.upazilas = [];
                        if (!districtId) return;
                        this.loadingUpazilas = true;
                        fetch('/ajax/upazilas/' + districtId)
                            .then(r => r.json())
                            .then(data => { this.upazilas = data; this.loadingUpazilas = false; })
                            .catch(() => { this.loadingUpazilas = false; });
                    }
                }">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Name --}}
                        <div class="md:col-span-2">
                            <x-input-label for="name" value="অ্যাম্বুলেন্সের নাম / ড্রাইভারের নাম" class="text-slate-700 font-bold" />
                            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full bg-slate-50 border-slate-200" :value="old('name')" required placeholder="যেমন: সেবা অ্যাম্বুলেন্স সার্ভিস" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        {{-- Phone --}}
                        <div>
                            <x-input-label for="phone" value="মোবাইল নম্বর" class="text-slate-700 font-bold" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-2 block w-full bg-slate-50 border-slate-200" :value="old('phone')" required placeholder="01XXXXXXXXX" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        {{-- Type --}}
                        <div>
                            <x-input-label for="type" value="অ্যাম্বুলেন্সের ধরন" class="text-slate-700 font-bold" />
                            <select id="type" name="type" class="mt-2 block w-full bg-slate-50 border-slate-200 rounded-xl focus:border-red-500 focus:ring-red-500 font-medium" required>
                                <option value="non-ac" {{ old('type') == 'non-ac' ? 'selected' : '' }}>Non-AC</option>
                                <option value="ac" {{ old('type') == 'ac' ? 'selected' : '' }}>AC</option>
                                <option value="icu" {{ old('type') == 'icu' ? 'selected' : '' }}>ICU / Life Support</option>
                                <option value="nicu" {{ old('type') == 'nicu' ? 'selected' : '' }}>NICU</option>
                                <option value="freezer" {{ old('type') == 'freezer' ? 'selected' : '' }}>Freezer Van</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        {{-- Location --}}
                        <div class="md:col-span-2 border-t border-slate-100 pt-6 mt-2">
                            <h4 class="font-bold text-slate-800 mb-4">লোকেশন তথ্য</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <select name="division_id" @change="fetchDistricts($event.target.value)" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-medium" required>
                                        <option value="">বিভাগ নির্বাচন</option>
                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('division_id')" class="mt-1" />
                                </div>
                                <div>
                                    <select name="district_id" @change="fetchUpazilas($event.target.value)" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-medium" required>
                                        <option value="" x-text="loadingDistricts ? 'লোড হচ্ছে...' : 'জেলা নির্বাচন'"></option>
                                        <template x-for="d in districts" :key="d.id">
                                            <option :value="d.id" x-text="d.name"></option>
                                        </template>
                                    </select>
                                    <x-input-error :messages="$errors->get('district_id')" class="mt-1" />
                                </div>
                                <div>
                                    <select name="upazila_id" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-medium" required>
                                        <option value="" x-text="loadingUpazilas ? 'লোড হচ্ছে...' : 'উপজেলা নির্বাচন'"></option>
                                        <template x-for="u in upazilas" :key="u.id">
                                            <option :value="u.id" x-text="u.name"></option>
                                        </template>
                                    </select>
                                    <x-input-error :messages="$errors->get('upazila_id')" class="mt-1" />
                                </div>
                            </div>
                        </div>

                        {{-- Vehicle Number (Optional) --}}
                        <div class="md:col-span-2 border-t border-slate-100 pt-6 mt-2">
                            <x-input-label for="vehicle_number" value="গাড়ির রেজিস্ট্রেশন নম্বর (ঐচ্ছিক)" class="text-slate-700 font-bold" />
                            <x-text-input id="vehicle_number" name="vehicle_number" type="text" class="mt-2 block w-full bg-slate-50 border-slate-200" :value="old('vehicle_number')" placeholder="যেমন: ঢাকা মেট্রো-ছ ৭১-XXXX" />
                            <p class="text-xs text-slate-500 mt-2 font-medium">গাড়ির নম্বর দিলে ভেরিফাই করতে সুবিধা হয়।</p>
                            <x-input-error :messages="$errors->get('vehicle_number')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end border-t border-slate-100 pt-6">
                        <x-primary-button class="bg-red-600 hover:bg-red-700 px-8 py-4 text-base rounded-2xl shadow-[0_8px_20px_rgba(239,68,68,0.3)]">
                            সাবমিট করুন
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
