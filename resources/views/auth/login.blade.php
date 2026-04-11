<x-auth-split-layout>
    @section('title', 'লগইন')
    
    <div class="mb-8 text-center">
        <h2 class="text-3xl items-center font-bold text-slate-800 mb-2">স্বাগতম ফিরে এসেছেন!</h2>
        <p class="text-slate-500">আপনার অ্যাকাউন্টে লগইন করতে বিস্তারিত তথ্য দিন</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">ইমেইল ঠিকানা</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="উদাহরণ: name@example.com" class="input-modern" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1">
                <label for="password" class="block text-sm font-medium text-slate-700">পাসওয়ার্ড</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-red-600 hover:text-red-700 hover:underline transition-colors">
                        পাসওয়ার্ড ভুলে গেছেন?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="input-modern" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-red-600 bg-slate-100 border-slate-300 rounded focus:ring-red-500 focus:ring-2 cursor-pointer transition-colors">
            <label for="remember_me" class="ml-2 text-sm text-slate-600 cursor-pointer select-none">
                আমাকে মনে রাখুন
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all hover:-translate-y-0.5 mt-2">
            লগইন করুন
        </button>
    </form>

    <!-- Social Login -->
    <div class="mt-8">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 text-slate-500 bg-white/80">অথবা</span>
            </div>
        </div>
        
        <div class="mt-6">
            <a href="{{ route('social.redirect', 'google') }}" class="w-full flex justify-center items-center py-2.5 px-4 border border-slate-200 rounded-xl shadow-sm bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                <svg class="h-5 w-5 mr-3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Google দিয়ে লগইন করুন
            </a>
        </div>
    </div>

    <!-- Registration Link -->
    <p class="mt-8 text-center text-sm text-slate-600">
        অ্যাকাউন্ট নেই? 
        <a href="{{ route('register') }}" class="font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">
            রেজিস্ট্রেশন করুন
        </a>
    </p>
</x-auth-split-layout>