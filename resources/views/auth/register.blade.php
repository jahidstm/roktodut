<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="role" :value="__('আমি হিসেবে যুক্ত হতে চাই:')" />
            <div class="flex items-center gap-4 mt-2">
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="recipient" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked onchange="toggleDonorFields()">
                    <span class="ml-2 text-sm text-gray-600">রক্তগ্রহীতা (Recipient)</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="role" value="donor" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" onchange="toggleDonorFields()">
                    <span class="ml-2 text-sm text-gray-600">রক্তদাতা (Donor)</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div id="donor_fields" class="hidden mt-4 p-4 border rounded-md bg-gray-50">
            <div class="mb-4">
                <x-input-label for="phone" :value="__('ফোন নম্বর *')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="blood_group" :value="__('রক্তের গ্রুপ *')" />
                <select id="blood_group" name="blood_group" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                    <option value="">সিলেক্ট করুন</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
                <x-input-error :messages="$errors->get('blood_group')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 bg-gray-100 text-gray-500">অথবা</span>
            </div>
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('social.redirect', 'google') }}" class="w-full inline-flex justify-center items-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Google দিয়ে অ্যাকাউন্ট তৈরি করুন
            </a>
        </div>
    </div>

    <script>
        function toggleDonorFields() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const donorFields = document.getElementById('donor_fields');
            
            if (role === 'donor') {
                donorFields.classList.remove('hidden');
            } else {
                donorFields.classList.add('hidden');
            }
        }
        // Run on page load in case of old input errors
        document.addEventListener('DOMContentLoaded', toggleDonorFields);
    </script>
</x-guest-layout>