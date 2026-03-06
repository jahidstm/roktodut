<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ ('স্বাগতম! আপনার অ্যাকাউন্টটি প্রায় প্রস্তুত। সিস্টেম ব্যবহার শুরু করতে অনুগ্রহ করে নিচের তথ্যগুলো দিন।') }}
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}">
        @csrf

        <div class="mt-4">
            <x-input-label for="role" :value="('আমি যুক্ত হতে চাই:')" />
            <div class="flex items-center gap-4 mt-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="recipient" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" checked onchange="toggleFields()">
                    <span class="ml-2 text-sm text-gray-600">রক্তগ্রহীতা</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="donor" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" onchange="toggleFields()">
                    <span class="ml-2 text-sm text-gray-600">রক্তদাতা</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div id="donor_fields" class="hidden mt-4 p-4 border border-red-100 rounded-md bg-red-50">
            <div class="mb-4">
                <x-input-label for="phone" :value="('ফোন নম্বর *')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="blood_group" :value="('রক্তের গ্রুপ *')" />
                <select id="blood_group" name="blood_group" class="border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm block mt-1 w-full">
                    <option value="">সিলেক্ট করুন</option>
                    <option value="A+">A+</option><option value="A-">A-</option>
                    <option value="B+">B+</option><option value="B-">B-</option>
                    <option value="O+">O+</option><option value="O-">O-</option>
                    <option value="AB+">AB+</option><option value="AB-">AB-</option>
                </select>
                <x-input-error :messages="$errors->get('blood_group')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="bg-red-600 hover:bg-red-700">
                {{ __('প্রোফাইল সেভ করুন') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function toggleFields() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const donorFields = document.getElementById('donor_fields');
            if (role === 'donor') { donorFields.classList.remove('hidden'); } 
            else { donorFields.classList.add('hidden'); }
        }
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</x-guest-layout>