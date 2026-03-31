<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address along with your donation location.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="division_id" :value="__('বিভাগ')" />
                <select id="division_id" name="division_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">বিভাগ নির্বাচন করুন</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('division_id')" />
            </div>

            <div>
                <x-input-label for="district_id" :value="__('জেলা')" />
                <select id="district_id" name="district_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled>
                    <option value="">জেলা নির্বাচন করুন</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('district_id')" />
            </div>

            <div>
                <x-input-label for="upazila_id" :value="__('উপজেলা/এরিয়া')" />
                <select id="upazila_id" name="upazila_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" disabled>
                    <option value="">উপজেলা নির্বাচন করুন</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('upazila_id')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    {{-- ⚙️ AJAX Script for Dynamic Dropdowns --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const divSelect = document.getElementById('division_id');
            const distSelect = document.getElementById('district_id');
            const upzSelect = document.getElementById('upazila_id');

            // বর্তমান ইউজারের সেভ করা আইডি (Pre-selection এর জন্য)
            const savedDiv = "{{ auth()->user()->division_id }}";
            const savedDist = "{{ auth()->user()->district_id }}";
            const savedUpz = "{{ auth()->user()->upazila_id }}";

            // ১. পেজ লোড হলেই বিভাগগুলো নিয়ে আসা
            fetch('/ajax/divisions')
                .then(res => res.json())
                .then(data => {
                    data.forEach(div => {
                        const selected = (div.id == savedDiv) ? 'selected' : '';
                        divSelect.innerHTML += `<option value="${div.id}" ${selected}>${div.name}</option>`;
                    });
                    // যদি আগে থেকে বিভাগ সেভ করা থাকে, তবে জেলা লোড ট্রিগার করো
                    if(savedDiv) divSelect.dispatchEvent(new Event('change'));
                });

            // ২. বিভাগ চেঞ্জ হলে জেলা লোড
            divSelect.addEventListener('change', function() {
                const divId = this.value;
                distSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                distSelect.disabled = true;
                upzSelect.innerHTML = '<option value="">উপজেলা নির্বাচন করুন</option>';
                upzSelect.disabled = true;

                if (divId) {
                    fetch(`/ajax/districts/${divId}`)
                        .then(res => res.json())
                        .then(data => {
                            distSelect.innerHTML = '<option value="">জেলা নির্বাচন করুন</option>';
                            distSelect.disabled = false;
                            data.forEach(dist => {
                                const selected = (dist.id == savedDist) ? 'selected' : '';
                                distSelect.innerHTML += `<option value="${dist.id}" ${selected}>${dist.name}</option>`;
                            });
                            // যদি জেলা সেভ করা থাকে, তবে উপজেলা লোড ট্রিগার করো
                            if(savedDist) distSelect.dispatchEvent(new Event('change'));
                        });
                }
            });

            // ৩. জেলা চেঞ্জ হলে উপজেলা লোড
            distSelect.addEventListener('change', function() {
                const distId = this.value;
                upzSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                upzSelect.disabled = true;

                if (distId) {
                    fetch(`/ajax/upazilas/${distId}`)
                        .then(res => res.json())
                        .then(data => {
                            upzSelect.innerHTML = '<option value="">উপজেলা নির্বাচন করুন</option>';
                            upzSelect.disabled = false;
                            data.forEach(upz => {
                                const selected = (upz.id == savedUpz) ? 'selected' : '';
                                upzSelect.innerHTML += `<option value="${upz.id}" ${selected}>${upz.name}</option>`;
                            });
                        });
                }
            });
        });
    </script>
</section>