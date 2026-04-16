@extends('layouts.app')

@section('title', 'আমাদের সম্পর্কে — রক্তদূত')

@section('content')

{{-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-red-700 via-rose-600 to-red-800">
    {{-- decorative blobs --}}
    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

    <div class="relative mx-auto max-w-4xl px-4 py-16 sm:py-20 text-center">
        <span class="inline-flex items-center gap-2 bg-white/15 backdrop-blur border border-white/20 text-white text-xs font-bold px-4 py-1.5 rounded-full mb-5">
            🩸 রক্তদূত প্ল্যাটফর্ম
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
            আমরা রক্ত এবং মানুষের<br class="hidden sm:block"> মধ্যে সেতু তৈরি করি
        </h1>
        <p class="mt-4 text-red-100 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed font-medium">
            রক্তদূত বাংলাদেশের প্রথম ভেরিফাইড ব্লাড ডোনেশন প্ল্যাটফর্ম — যেখানে প্রতিটি ডোনার পরিচয় যাচাইকৃত, প্রতিটি রক্তের অনুরোধ নিরাপদ।
        </p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     STATS STRIP
══════════════════════════════════════════════════════════ --}}
<section class="bg-white border-b border-slate-100">
    <div class="mx-auto max-w-4xl px-4 py-8 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
        @php
        $stats = [
            ['value' => '৫,০০০+', 'label' => 'ভেরিফাইড ডোনার',   'icon' => '🛡️'],
            ['value' => '১,২০০+', 'label' => 'সফল রক্তদান',       'icon' => '🩸'],
            ['value' => '৬৪',    'label' => 'জেলায় সক্রিয়',     'icon' => '🗺️'],
            ['value' => '৩,৬০০+','label' => 'জীবন বাঁচানো হয়েছে', 'icon' => '❤️'],
        ];
        @endphp
        @foreach($stats as $s)
        <div>
            <div class="text-2xl mb-1">{{ $s['icon'] }}</div>
            <div class="text-2xl sm:text-3xl font-black text-red-600">{{ $s['value'] }}</div>
            <div class="text-xs sm:text-sm font-semibold text-slate-500 mt-0.5">{{ $s['label'] }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MISSION & STORY
══════════════════════════════════════════════════════════ --}}
<section class="mx-auto max-w-4xl px-4 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">

        {{-- Text --}}
        <div>
            <span class="inline-block text-xs font-black text-red-600 uppercase tracking-widest mb-3">আমাদের গল্প</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug mb-4">
                একটি জরুরি মুহূর্ত থেকে<br>জন্ম নিল একটি প্ল্যাটফর্ম
            </h2>
            <div class="space-y-4 text-slate-600 text-sm leading-relaxed font-medium">
                <p>
                    ২০২৪ সালে একটি রাস্তার দুর্ঘটনায় জরুরি রক্তের প্রয়োজন পড়ে। সেদিন ফেসবুক পোস্ট, ফোন কল — কিছুতেই সময়মতো সঠিক গ্রুপের ডোনার পাওয়া যায়নি। সেই অভিজ্ঞতা থেকে অনুপ্রাণিত হয়ে আমাদের দল একটি নির্ভরযোগ্য, ভেরিফাইড সিস্টেম তৈরির লক্ষ্যে কাজ শুরু করে।
                </p>
                <p>
                    রক্তদূত শুধু একটি ডোনার-তালিকা নয় — এটি একটি পূর্ণ ইকোসিস্টেম। NID ভেরিফিকেশন, রিয়েল-টাইম নোটিফিকেশন, গ্যামিফিকেশন, এবং অর্গানাইজেশন ইন্টিগ্রেশনের মাধ্যমে আমরা রক্তের অনুরোধ এবং ডোনারের মধ্যে সংযোগ যতটা সম্ভব নিরাপদ ও দ্রুত করতে চাই।
                </p>
            </div>
        </div>

        {{-- Visual Card --}}
        <div class="relative">
            <div class="bg-gradient-to-br from-red-50 to-rose-50 border border-red-100 rounded-3xl p-7">
                <div class="space-y-4">
                    @php
                    $values = [
                        ['emoji' => '🛡️', 'title' => 'বিশ্বাসযোগ্যতা', 'desc' => 'প্রতিটি ডোনারের পরিচয় NID দিয়ে যাচাই করা হয়।'],
                        ['emoji' => '⚡', 'title' => 'দ্রুততা',        'desc' => 'রিয়েল-টাইম নোটিফিকেশনে সেকেন্ডের মধ্যে ডোনার খুঁজুন।'],
                        ['emoji' => '🔒', 'title' => 'গোপনীয়তা',     'desc' => 'ডোনারের নম্বর লুকানো — শুধু অনুরোধকারী দেখতে পান।'],
                        ['emoji' => '🤝', 'title' => 'কমিউনিটি',      'desc' => 'হাসপাতাল, ক্লাব ও ব্যক্তি — সবাই এক প্ল্যাটফর্মে।'],
                    ];
                    @endphp
                    @foreach($values as $v)
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-9 h-9 rounded-xl bg-white border border-red-100 shadow-sm flex items-center justify-center text-lg">{{ $v['emoji'] }}</div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $v['title'] }}</p>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ $v['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════════════ --}}
<section class="bg-slate-50 border-y border-slate-100">
    <div class="mx-auto max-w-4xl px-4 py-14">
        <div class="text-center mb-10">
            <span class="inline-block text-xs font-black text-red-600 uppercase tracking-widest mb-2">কার্যক্রম</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">আমরা যেভাবে কাজ করি</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @php
            $steps = [
                ['num' => '০১', 'icon' => '📝', 'title' => 'রেজিস্ট্রেশন ও ভেরিফিকেশন',
                 'desc' => 'ডোনার রেজিস্ট্রেশন করেন এবং NID আপলোড করে পরিচয় যাচাই করান। ভেরিফাইড ব্যাজ পান।'],
                ['num' => '০২', 'icon' => '🔔', 'title' => 'রক্তের অনুরোধ ও ম্যাচিং',
                 'desc' => 'রোগীর পরিজন রক্তের অনুরোধ পাঠান। সিস্টেম স্বয়ংক্রিয়ভাবে কাছের ডোনারদের নোটিফাই করে।'],
                ['num' => '০৩', 'icon' => '✅', 'title' => 'দান নিশ্চিত ও পয়েন্ট',
                 'desc' => 'রক্তদান সম্পন্ন হলে উভয় পক্ষ নিশ্চিত করেন। ডোনার পয়েন্ট ও ব্যাজ পান।'],
            ];
            @endphp
            @foreach($steps as $step)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-[11px] font-black text-red-500 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full">{{ $step['num'] }}</span>
                    <span class="text-2xl">{{ $step['icon'] }}</span>
                </div>
                <h3 class="text-sm font-black text-slate-800 mb-1.5">{{ $step['title'] }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     TEAM
══════════════════════════════════════════════════════════ --}}
<section class="mx-auto max-w-4xl px-4 py-14">
    <div class="text-center mb-10">
        <span class="inline-block text-xs font-black text-red-600 uppercase tracking-widest mb-2">আমাদের দল</span>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900">যারা তৈরি করেছেন রক্তদূত</h2>
        <p class="mt-2 text-sm text-slate-500 font-medium">একটি ছোট কিন্তু নিবেদিতপ্রাণ SWE Capstone দল</p>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
        @php
        $team = [
            ['initials' => 'MA', 'name' => 'মোহাম্মদ আলিফ',   'role' => 'ফুলস্ট্যাক লিড',      'color' => 'bg-red-600'],
            ['initials' => 'SR', 'name' => 'সামিরা রহমান',    'role' => 'UI/UX ডিজাইনার',     'color' => 'bg-rose-500'],
            ['initials' => 'TH', 'name' => 'তানভীর হাসান',    'role' => 'ব্যাকএন্ড ইঞ্জিনিয়ার', 'color' => 'bg-pink-600'],
            ['initials' => 'NK', 'name' => 'নাওমি খানম',      'role' => 'কিউএ ও ডকুমেন্টেশন', 'color' => 'bg-red-700'],
        ];
        @endphp
        @foreach($team as $member)
        <div class="text-center group">
            <div class="mx-auto w-16 h-16 sm:w-20 sm:h-20 rounded-2xl {{ $member['color'] }} flex items-center justify-center mb-3 shadow-md group-hover:scale-105 transition-transform duration-200">
                <span class="text-white font-black text-lg sm:text-xl">{{ $member['initials'] }}</span>
            </div>
            <p class="text-sm font-bold text-slate-800">{{ $member['name'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $member['role'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     CTA
══════════════════════════════════════════════════════════ --}}
<section class="bg-gradient-to-r from-slate-900 to-slate-800">
    <div class="mx-auto max-w-4xl px-4 py-12 text-center">
        <h2 class="text-xl sm:text-2xl font-black text-white mb-2">আপনিও হন রক্তদূতের অংশ</h2>
        <p class="text-slate-400 text-sm mb-6 font-medium">আজই রেজিস্ট্রেশন করুন এবং একটি জীবন বাঁচানোর সুযোগ নিন।</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
               class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-black text-sm px-6 py-3 rounded-xl shadow-sm transition-all">
                🩸 অ্যাকাউন্ট খুলুন
            </a>
            <a href="{{ route('contact.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-sm px-6 py-3 rounded-xl transition-all">
                ✉️ যোগাযোগ করুন
            </a>
        </div>
    </div>
</section>

@endsection
