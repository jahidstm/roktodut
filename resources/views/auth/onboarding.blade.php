<x-guest-layout>
    <div class="mb-6 text-sm text-gray-600 border-l-4 border-red-500 pl-3">
        <h2 class="font-bold text-lg text-gray-800">স্বাগতম, {{ auth()->user()->name }}!</h2>
        <p class="mt-1">আপনার অ্যাকাউন্টটি প্রায় প্রস্তুত। স্মার্ট সার্চ ইঞ্জিনে যুক্ত হতে অনুগ্রহ করে আপনার লোকেশনটি কনফার্ম করুন।</p>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}">
        @csrf

        <div class="space-y-5">
            {{-- বিভাগ --}}
            <div>
                <x-input-label for="division" :value="('বিভাগ *')" />
                <select id="division" name="division_id" class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm block mt-1 w-full" required>
                    <option value="">বিভাগ সিলেক্ট করুন</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('division_id')" class="mt-2" />
            </div>

            {{-- জেলা --}}
            <div>
                <x-input-label for="district" :value="('জেলা *')" />
                <select id="district" name="district_id" class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm block mt-1 w-full bg-gray-50" required disabled>
                    <option value="">প্রথমে বিভাগ সিলেক্ট করুন</option>
                </select>
                <x-input-error :messages="$errors->get('district_id')" class="mt-2" />
            </div>

            {{-- উপজেলা --}}
            <div>
                <x-input-label for="upazila" :value="('উপজেলা / এরিয়া *')" />
                <select id="upazila" name="upazila_id" class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm block mt-1 w-full bg-gray-50" required disabled>
                    <option value="">প্রথমে জেলা সিলেক্ট করুন</option>
                </select>
                <x-input-error :messages="$errors->get('upazila_id')" class="mt-2" />
            </div>

            {{-- শেষ রক্তদানের তারিখ (শুধুমাত্র ডোনারদের জন্য) --}}
            @if(auth()->user()->role === 'donor')
                <div class="p-4 border border-red-100 rounded-md bg-red-50">
                    <x-input-label for="last_donation_date" :value="('শেষ রক্তদানের তারিখ (ঐচ্ছিক)')" />
                    <x-text-input id="last_donation_date" class="block mt-1 w-full text-gray-700" type="date" name="last_donation_date" :value="old('last_donation_date')" max="{{ date('Y-m-d') }}" />
                    <p class="text-xs text-gray-500 mt-1">কখনো রক্ত না দিয়ে থাকলে ফাঁকা রাখুন।</p>
                    <x-input-error :messages="$errors->get('last_donation_date')" class="mt-2" />
                </div>
            @endif
        </div>

        <div class="flex items-center justify-end mt-8">
            <x-primary-button class="w-full justify-center bg-red-600 hover:bg-red-700 py-3">
                {{ __('প্রোফাইল সেভ করুন ও ড্যাশবোর্ডে যান') }}
            </x-primary-button>
        </div>
    </form>

    {{-- 🎯 AJAX Location Loaders --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const divisionSelect = document.getElementById('division');
            const districtSelect = document.getElementById('district');
            const upazilaSelect = document.getElementById('upazila');

            // Division Change -> Load Districts
            divisionSelect.addEventListener('change', function() {
                const divisionId = this.value;
                
                districtSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                districtSelect.disabled = true;
                upazilaSelect.innerHTML = '<option value="">প্রথমে জেলা সিলেক্ট করুন</option>';
                upazilaSelect.disabled = true;

                if(divisionId) {
                    fetch(`/ajax/districts/${divisionId}`)
                        .then(response => response.json())
                        .then(data => {
                            districtSelect.innerHTML = '<option value="">জেলা সিলেক্ট করুন</option>';
                            data.forEach(district => {
                                districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                            });
                            districtSelect.disabled = false;
                            districtSelect.classList.remove('bg-gray-50');
                        });
                } else {
                    districtSelect.innerHTML = '<option value="">প্রথমে বিভাগ সিলেক্ট করুন</option>';
                    districtSelect.classList.add('bg-gray-50');
                }
            });

            // District Change -> Load Upazilas
            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                
                upazilaSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                upazilaSelect.disabled = true;

                if(districtId) {
                    fetch(`/ajax/upazilas/${districtId}`)
                        .then(response => response.json())
                        .then(data => {
                            upazilaSelect.innerHTML = '<option value="">উপজেলা সিলেক্ট করুন</option>';
                            data.forEach(upazila => {
                                upazilaSelect.innerHTML += `<option value="${upazila.id}">${upazila.name}</option>`;
                            });
                            upazilaSelect.disabled = false;
                            upazilaSelect.classList.remove('bg-gray-50');
                        });
                } else {
                    upazilaSelect.innerHTML = '<option value="">প্রথমে জেলা সিলেক্ট করুন</option>';
                    upazilaSelect.classList.add('bg-gray-50');
                }
            });
        });
    </script>
</x-guest-layout>