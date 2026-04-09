@extends('layouts.app')

@section('title', 'রক্ত দিন - বাঁচান একটি প্রাণ | রক্তদূত')

@section('content')
<div class="relative overflow-hidden bg-white">
    {{-- Background accents --}}
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-red-50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 opacity-70"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-rose-50 rounded-full blur-3xl translate-y-1/3 -translate-x-1/4 opacity-60"></div>
    
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        {{-- Hero Header --}}
        <div class="text-center max-w-4xl mx-auto mb-16">
            <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-black tracking-wide border border-red-100 mb-6 shadow-sm">
                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                জীবন রক্ষাকারী মিশন
            </span>
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] mb-6 tracking-tight">
                আপনার এক ফোঁটা রক্ত, <br class="hidden sm:block"/> <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-rose-500">বাঁচতে পারে একটি প্রাণ</span>
            </h1>
            <p class="text-lg sm:text-xl text-slate-600 mb-10 max-w-2xl mx-auto font-medium leading-relaxed">
                রক্তদান একটি মহৎ কাজ। আমাদের স্মার্ট ও আধুনিক নেটওয়ার্কে যোগ দিন, এবং আপনার আশেপাশের রোগীদের জন্য আশার আলো হয়ে উঠুন।
            </p>
            
            {{-- Smart CTA --}}
            <div class="flex justify-center">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-gradient-to-r from-red-600 to-rose-600 text-white px-8 py-4 rounded-2xl font-extrabold text-lg shadow-xl shadow-red-600/30 hover:shadow-red-600/40 hover:-translate-y-1 transition-all duration-300 gap-2 w-full sm:w-auto">
                        ডোনার হিসেবে যুক্ত হোন
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center bg-slate-900 text-white px-8 py-4 rounded-2xl font-extrabold text-lg shadow-xl shadow-slate-900/20 hover:-translate-y-1 transition-all duration-300 gap-2 w-full sm:w-auto">
                        ড্যাশবোর্ডে যান
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </a>
                @endauth
            </div>
        </div>

        {{-- Value Propositions Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 relative z-10">
            
            {{-- Feature 1 --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -z-10 group-hover:scale-[2] transition-transform duration-500 ease-out"></div>
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-emerald-200 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">NID Verified Trust</h3>
                <p class="text-slate-600 font-medium leading-relaxed group-hover:text-slate-700 transition-colors">
                    আমাদের প্ল্যাটফর্মের ডোনাররা জাতীয় পরিচয়পত্র দ্বারা ভেরিফাইড। আপনার তথ্য শতভাগ সুরক্ষিত ও নির্ভরযোগ্য থাকবে।
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -z-10 group-hover:scale-[2] transition-transform duration-500 ease-out"></div>
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-blue-200 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Dynamic Smart Card</h3>
                <p class="text-slate-600 font-medium leading-relaxed group-hover:text-slate-700 transition-colors">
                    ভেরিফায়েড ডোনারদের জন্য ডিজিটাল QR স্মার্ট কার্ড, যার মাধ্যমে আপনার রক্তদানের স্ট্যাটাস সহজেই প্রমাণ করা যাবে।
                </p>
            </div>

            {{-- Feature 3 --}}
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02] transition-all duration-300 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-bl-full -z-10 group-hover:scale-[2] transition-transform duration-500 ease-out"></div>
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm border border-amber-200 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Gamification Badges</h3>
                <p class="text-slate-600 font-medium leading-relaxed group-hover:text-slate-700 transition-colors">
                    রক্তদানের মাধ্যমে পয়েন্ট অর্জন করুন এবং প্ল্যাটিনাম, গোল্ডেন সহ বিভিন্ন আকর্ষণীয় রিবন ও ব্যাজ আনলক করুন।
                </p>
            </div>

        </div>

        {{-- Social Proof Stat Section --}}
        <div class="mt-28 mb-10 text-center relative pointer-events-none">
            {{-- Subtle bg accent --}}
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-32 bg-red-100 blur-3xl rounded-full opacity-40 -z-10"></div>
            
            <p class="text-slate-600 text-lg sm:text-2xl font-bold mb-4 tracking-wide">আমাদের প্ল্যাটফর্মে এখন পর্যন্ত</p>
            <h2 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight">
                @php
                    $engNum = ['0','1','2','3','4','5','6','7','8','9'];
                    $bngNum = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
                    $vDonorsBn = str_replace($engNum, $bngNum, $verifiedDonors);
                    $lSavedBn = str_replace($engNum, $bngNum, $livesSaved);
                @endphp
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-rose-500 font-black px-1">{{ $vDonorsBn }}+</span> ডোনার যাচাইকৃত হয়েছেন এবং <br class="hidden lg:block"/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-500 font-black px-1 mt-3 sm:mt-0 inline-block">{{ $lSavedBn }}+</span> জীবন বাঁচানো হয়েছে।
            </h2>
        </div>
        
    </div>
</div>
@endsection
