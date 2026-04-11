<x-auth-split-layout>
    @section('title', 'রেজিস্ট্রেশন')
    
    <div class="mb-8 text-center pt-2">
        <h2 class="text-3xl items-center font-bold text-slate-800 mb-2">নতুন অ্যাকাউন্ট তৈরি করুন</h2>
        <p class="text-slate-500">রক্তদান কার্যক্রমে যুক্ত হতে আপনার তথ্য দিন</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">আপনার নাম</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="আপনার পূর্ণ নাম" class="input-modern" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">ইমেইল ঠিকানা</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@example.com" class="input-modern" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Role Selection -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">আমি হিসেবে যুক্ত হতে চাই:</label>
            <div class="flex items-center gap-6 bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <label class="flex items-center cursor-pointer group flex-1">
                    <div class="relative flex items-center justify-center">
                        <input type="radio" name="role" value="recipient" class="peer sr-only" {{ old('role', 'recipient') === 'recipient' ? 'checked' : '' }} onchange="toggleDonorFields()">
                        <div class="w-5 h-5 border-2 border-slate-300 rounded-full peer-checked:border-red-600 peer-checked:border-[6px] transition-all bg-white"></div>
                    </div>
                    <span class="ml-2 text-sm text-slate-700 group-hover:text-slate-900 font-medium">রক্তগ্রহীতা</span>
                </label>
                <label class="flex items-center cursor-pointer group flex-1 border-l border-slate-100 pl-4">
                    <div class="relative flex items-center justify-center">
                        <input type="radio" name="role" value="donor" class="peer sr-only" {{ old('role') === 'donor' ? 'checked' : '' }} onchange="toggleDonorFields()">
                        <div class="w-5 h-5 border-2 border-slate-300 rounded-full peer-checked:border-red-600 peer-checked:border-[6px] transition-all bg-white"></div>
                    </div>
                    <span class="ml-2 text-sm text-slate-700 group-hover:text-slate-900 font-medium">রক্তদাতা</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Donor Specific Fields -->
        <div id="donor_fields" class="hidden space-y-4 p-4 border border-red-100 rounded-2xl bg-red-50/50">
            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">ফোন নম্বর <span class="text-red-500">*</span></label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" class="input-modern bg-white" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1 text-sm text-red-600" />
            </div>

            <div>
                <label for="blood_group" class="block text-sm font-medium text-slate-700 mb-1">রক্তের গ্রুপ <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select id="blood_group" name="blood_group" class="input-modern appearance-none bg-white cursor-pointer pr-10">
                        <option value="">রক্তের গ্রুপ নির্বাচন করুন</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('blood_group')" class="mt-1 text-sm text-red-600" />
            </div>
        </div>

        <!-- Passwords Row to save vertical space -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">পাসওয়ার্ড</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="সর্বনিম্ন ৮ অক্ষর" class="input-modern" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">নিশ্চিত করুন</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="পুনরায় লিখুন" class="input-modern" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all hover:-translate-y-0.5 mt-4">
            রেজিস্ট্রেশন করুন
        </button>
    </form>

    <!-- Social Login -->
    <div class="mt-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 text-slate-500 bg-white/80">অথবা</span>
            </div>
        </div>
        
        <div class="mt-5">
            <a href="{{ route('social.redirect', 'google') }}" class="w-full flex justify-center items-center py-2.5 px-4 border border-slate-200 rounded-xl shadow-sm bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Google দিয়ে লগইন করুন
            </a>
        </div>
    </div>

    <!-- Login Link -->
    <p class="mt-6 text-center text-sm text-slate-600 pb-2">
        অ্যাকাউন্ট আছে? 
        <a href="{{ route('login') }}" class="font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">
            লগইন করুন
        </a>
    </p>

    <script>
        function toggleDonorFields() {
            const roleInput = document.querySelector('input[name="role"]:checked');
            if (!roleInput) return;
            
            const role = roleInput.value;
            const donorFields = document.getElementById('donor_fields');
            
            if (role === 'donor') {
                donorFields.classList.remove('hidden');
                donorFields.style.animation = "fadeIn 0.3s ease-in-out";
            } else {
                donorFields.classList.add('hidden');
            }
        }
        
        if(!document.getElementById('fade-style')) {
            const style = document.createElement('style');
            style.id = 'fade-style';
            style.innerHTML = `
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(-5px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(style);
        }

        // Run on page load
        document.addEventListener('DOMContentLoaded', toggleDonorFields);
    </script>
</x-auth-split-layout>