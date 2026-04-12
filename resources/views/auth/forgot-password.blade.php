<x-auth-split-layout>
    @section('title', 'পাসওয়ার্ড ভুলে গেছেন?')
    
    <div class="mb-8 text-center pt-2">
        <h2 class="text-3xl items-center font-bold text-slate-800 mb-2">পাসওয়ার্ড পুনরুদ্ধার</h2>
        <p class="text-slate-500">আপনার ইমেইল ঠিকানা দিন, আমরা একটি রিসেট লিংক পাঠিয়ে দেব।</p>
    </div>

    <!-- Enhanced Enumeration Protection Status -->
    @if(session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 p-4 border border-emerald-200 shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-semibold text-emerald-800 leading-snug">
                যদি আপনার প্রদত্ত ইমেইলটি আমাদের সিস্টেমে থেকে থাকে, তবে একটি পাসওয়ার্ড রিসেট লিংক ইমেইলে পাঠানো হয়েছে। অনুগ্রহ করে স্প্যাম ফোল্ডারসহ ইনবক্স চেক করুন।
            </span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">ইমেইল ঠিকানা <span class="text-red-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="আপনার নিবন্ধিত ইমেইল" autocomplete="email" class="input-modern" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-xl shadow-lg shadow-red-600/30 transition-all hover:-translate-y-0.5">
            রিসেট লিংক পাঠান
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600">
        পাসওয়ার্ড মনে পড়েছে? 
        <a href="{{ route('login') }}" class="font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">
            লগইন করুন
        </a>
    </p>
</x-auth-split-layout>
