<x-auth-split-layout>
    @section('title', 'নতুন পাসওয়ার্ড সেট করুন')
    
    <div class="mb-8 text-center pt-2">
        <h2 class="text-3xl items-center font-bold text-slate-800 mb-2">নতুন পাসওয়ার্ড</h2>
        <p class="text-slate-500">আপনার অ্যাকাউন্টের জন্য একটি শক্তিশালী নতুন পাসওয়ার্ড দিন</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">ইমেইল ঠিকানা <span class="text-red-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="input-modern" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">নতুন পাসওয়ার্ড <span class="text-red-500">*</span></label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="সর্বনিম্ন ৮ অক্ষর" class="input-modern" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">পাসওয়ার্ড নিশ্চিত করুন <span class="text-red-500">*</span></label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="পুনরায় লিখুন" class="input-modern" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
        </div>

        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all hover:-translate-y-0.5 mt-2">
            পাসওয়ার্ড পরিবর্তন করুন
        </button>
    </form>
</x-auth-split-layout>
