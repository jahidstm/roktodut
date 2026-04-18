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
            রক্তদূত বাংলাদেশের একটি স্বেচ্ছাসেবী ভিত্তিক রক্তদান সমন্বয় প্ল্যাটফর্ম — যেখানে ডোনারের পরিচয় ম্যানুয়ালি যাচাইযোগ্য, রক্তের অনুরোধ দ্রুত ও নিরাপদ।
        </p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MEDICAL DISCLAIMER NOTICE
══════════════════════════════════════════════════════════ --}}
<div class="bg-amber-50 border-b border-amber-200">
    <div class="mx-auto max-w-4xl px-4 py-3 flex items-start gap-3">
        <span class="text-amber-500 text-base shrink-0 mt-0.5">⚠️</span>
        <p class="text-xs text-amber-800 font-semibold leading-relaxed">
            <strong>গুরুত্বপূর্ণ ডিসক্লেইমার:</strong> রক্তদূত একটি স্বেচ্ছাসেবী সংযোগ প্ল্যাটফর্ম। এটি কোনো চিকিৎসা পরামর্শ, রোগ নির্ণয় বা চিকিৎসা সেবা প্রদান করে না। রক্তগ্রহণের আগে অবশ্যই রেজিস্টার্ড চিকিৎসক বা হাসপাতালের পরামর্শ নিন এবং প্রয়োজনীয় ক্রস-ম্যাচিং ও স্ক্রিনিং নিশ্চিত করুন।
        </p>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     STATS STRIP
══════════════════════════════════════════════════════════ --}}
<section class="bg-white border-b border-slate-100">
    <div class="mx-auto max-w-4xl px-4 py-8 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
        @php
        $stats = [
            ['value' => '৫,০০০+', 'label' => 'নিবন্ধিত ডোনার',       'icon' => '🛡️'],
            ['value' => '৬৪',     'label' => 'জেলায় সক্রিয়',         'icon' => '🗺️'],
            ['value' => '৮টি',    'label' => 'রক্তের গ্রুপ সমর্থিত', 'icon' => '🩸'],
            ['value' => '২৪/৭',   'label' => 'অনুরোধ পাঠানো সম্ভব',  'icon' => '⏰'],
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
                    রাতের বেলায় হাসপাতালে দৌড়াদৌড়ি, ফেসবুকে পোস্ট, একের পর এক ফোন — তবু সঠিক গ্রুপের ডোনার সময়মতো পাওয়া গেল না। এই হতাশার অভিজ্ঞতা থেকেই রক্তদূতের যাত্রা শুরু হয়।
                </p>
                <p>
                    আমরা বিশ্বাস করি, একটি বিশ্বস্ত ডিজিটাল সেতু থাকলে রক্তের সংকট অনেকটাই কমানো সম্ভব। রক্তদূত সেই সেতু — ডোনার ও অনুরোধকারীর মধ্যে দ্রুততম, নিরাপদ সংযোগ।
                </p>
            </div>
        </div>

        {{-- Mission/Vision Card --}}
        <div class="relative">
            <div class="bg-gradient-to-br from-red-50 to-rose-50 border border-red-100 rounded-3xl p-7">
                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-black text-red-600 uppercase tracking-widest mb-1">🎯 মিশন</p>
                        <p class="text-sm font-semibold text-slate-700 leading-relaxed">
                            বাংলাদেশের প্রতিটি জেলায় স্বেচ্ছাসেবী রক্তদাতা ও রোগীর পরিজনের মধ্যে দ্রুত, নির্ভরযোগ্য এবং নিরাপদ সংযোগ তৈরি করা।
                        </p>
                    </div>
                    <div class="border-t border-red-100 pt-4">
                        <p class="text-xs font-black text-rose-600 uppercase tracking-widest mb-1">🔭 ভিশন</p>
                        <p class="text-sm font-semibold text-slate-700 leading-relaxed">
                            এমন একটি বাংলাদেশ, যেখানে রক্তের অভাবে কোনো জীবন হারাতে হবে না — কারণ সঠিক ডোনার সঠিক সময়ে পৌঁছাবেই।
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     HOW ROKTODUT IS DIFFERENT
══════════════════════════════════════════════════════════ --}}
<section class="bg-slate-50 border-y border-slate-100">
    <div class="mx-auto max-w-4xl px-4 py-14">
        <div class="text-center mb-10">
            <span class="inline-block text-xs font-black text-red-600 uppercase tracking-widest mb-2">আমাদের বৈশিষ্ট্য</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">রক্তদূত কেন আলাদা</h2>
            <p class="mt-2 text-sm text-slate-500 font-medium max-w-xl mx-auto">
                শুধু খোঁজার তালিকা নয় — একটি সম্পূর্ণ নিরাপদ সমন্বয় ব্যবস্থা।
            </p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            @php
            $features = [
                [
                    'icon'  => '🔍',
                    'title' => 'পাবলিক ডোনার সার্চ (লগইন ছাড়া)',
                    'desc'  => 'যেকেউ লগইন ছাড়াই রক্তের গ্রুপ, জেলা ও উপজেলা দিয়ে ডোনার খুঁজতে পারেন। জরুরি মুহূর্তে সাইন-আপের ঝামেলা নেই।',
                    'color' => 'bg-blue-50 border-blue-100',
                ],
                [
                    'icon'  => '⚡',
                    'title' => 'স্মার্ট প্রায়রিটি সর্টিং',
                    'desc'  => 'সার্চ রেজাল্ট শুধু নামের তালিকা নয় — যোগ্যতার তারিখ, শেষ দানের ব্যবধান ও লোকেশন বিবেচনায় সবচেয়ে উপযুক্ত ডোনার আগে দেখানো হয়।',
                    'color' => 'bg-emerald-50 border-emerald-100',
                ],
                [
                    'icon'  => '🛡️',
                    'title' => 'প্রাইভেসি শিল্ড',
                    'desc'  => 'ডোনারের ফোন নম্বর ডিফল্টভাবে মাস্কড। দেখতে হলে OTP চ্যালেঞ্জ পাস করতে হয়; প্রতিদিন সীমিত সংখ্যক নম্বর দেখার সুযোগ — অপব্যবহার রোধে রেট লিমিট সক্রিয়।',
                    'color' => 'bg-orange-50 border-orange-100',
                ],
                [
                    'icon'  => '🪪',
                    'title' => 'NID ভেরিফিকেশন ও Org যাচাই',
                    'desc'  => 'ডোনার স্বেচ্ছায় NID আপলোড করে "ভেরিফাইড" ব্যাজ পেতে পারেন — অ্যাডমিন ম্যানুয়ালি যাচাই করেন। হাসপাতাল ও ক্লাবও অর্গানাইজেশন ভেরিফিকেশনের মাধ্যমে প্ল্যাটফর্মে যুক্ত হতে পারে।',
                    'color' => 'bg-red-50 border-red-100',
                ],
            ];
            @endphp
            @foreach($features as $f)
            <div class="rounded-2xl border {{ $f['color'] }} p-5 flex gap-4 items-start">
                <div class="shrink-0 w-10 h-10 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center justify-center text-xl">{{ $f['icon'] }}</div>
                <div>
                    <p class="text-sm font-black text-slate-800 mb-1">{{ $f['title'] }}</p>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════════════ --}}
<section class="mx-auto max-w-4xl px-4 py-14">
    <div class="text-center mb-10">
        <span class="inline-block text-xs font-black text-red-600 uppercase tracking-widest mb-2">কার্যক্রম</span>
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900">আমরা যেভাবে কাজ করি</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @php
        $steps = [
            ['num' => '০১', 'icon' => '📝', 'title' => 'নিবন্ধন ও প্রোফাইল',
             'desc' => 'ডোনার নিজের রক্তের গ্রুপ, লোকেশন ও উপলব্ধতা দিয়ে রেজিস্ট্রেশন করেন। ঐচ্ছিকভাবে NID আপলোড করে ভেরিফাইড ব্যাজ পান।'],
            ['num' => '০২', 'icon' => '🔔', 'title' => 'অনুরোধ ও ম্যাচিং',
             'desc' => 'রোগীর পরিজন রক্তের গ্রুপ ও লোকেশন দিয়ে অনুরোধ পাঠান। সিস্টেম কাছের উপযুক্ত ডোনারদের নোটিফাই করে।'],
            ['num' => '০৩', 'icon' => '🤝', 'title' => 'সংযোগ ও নিশ্চিতকরণ',
             'desc' => 'অনুরোধকারী OTP চ্যালেঞ্জ পাস করে ডোনারের সাথে যোগাযোগ করেন। হাসপাতালে স্ক্রিনিং ও ক্রস-ম্যাচিং করে রক্তদান সম্পন্ন হয়।'],
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
</section>

{{-- ══════════════════════════════════════════════════════════
     VALUES
══════════════════════════════════════════════════════════ --}}
<section class="bg-slate-50 border-t border-slate-100">
    <div class="mx-auto max-w-4xl px-4 py-14">
        <div class="text-center mb-10">
            <span class="inline-block text-xs font-black text-red-600 uppercase tracking-widest mb-2">আমাদের মূল্যবোধ</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">আমরা যা বিশ্বাস করি</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @php
            $values = [
                ['emoji' => '🛡️', 'title' => 'বিশ্বাসযোগ্যতা', 'desc' => 'ডামি তথ্য বা অযাচাইকৃত দাবি ছাড়াই একটি স্বচ্ছ, সৎ প্ল্যাটফর্ম পরিচালনা করি।'],
                ['emoji' => '🔒', 'title' => 'গোপনীয়তা',     'desc' => 'ডোনারের পরিচয় প্রাইভেসি শিল্ড দিয়ে সুরক্ষিত। তথ্য কখনো তৃতীয় পক্ষের কাছে বিক্রি হয় না।'],
                ['emoji' => '🤝', 'title' => 'সম্প্রদায়',    'desc' => 'ব্যক্তি, হাসপাতাল ও ক্লাব — সবাইকে একটি কমন প্ল্যাটফর্মে যুক্ত করে রক্তদানকে সহজ করাই লক্ষ্য।'],
            ];
            @endphp
            @foreach($values as $v)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 text-center shadow-sm">
                <div class="text-3xl mb-3">{{ $v['emoji'] }}</div>
                <p class="text-sm font-black text-slate-800 mb-1">{{ $v['title'] }}</p>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $v['desc'] }}</p>
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
            ['initials' => 'JH', 'name' => 'Jahid Hasan', 'role' => 'Lead Backend, Database Architecture, Security', 'github' => '@jahidstm', 'github_url' => 'https://github.com/jahidstm', 'color' => 'bg-red-600'],
            ['initials' => 'AK', 'name' => 'Md. Alif Khan', 'role' => 'Frontend Refactoring, API Integration, UI Components', 'github' => '@3alif', 'github_url' => 'https://github.com/3alif', 'color' => 'bg-rose-500'],
            ['initials' => 'NT', 'name' => 'Nohzat Tabassum', 'role' => 'UI/UX, OAuth Integration, System Documentation', 'github' => '@NohzatTabassum', 'github_url' => 'https://github.com/NohzatTabassum', 'color' => 'bg-pink-600'],
            ['initials' => 'MM', 'name' => 'Mst. Moumita Rahman Meem', 'role' => 'Database Seeders, Localization, Demo Data', 'github' => '@Meem-1137', 'github_url' => 'https://github.com/Meem-1137', 'color' => 'bg-red-700'],
        ];
        @endphp
        @foreach($team as $member)
        <div class="text-center group">
            <div class="mx-auto w-16 h-16 sm:w-20 sm:h-20 rounded-2xl {{ $member['color'] }} flex items-center justify-center mb-3 shadow-md group-hover:scale-105 transition-transform duration-200">
                <span class="text-white font-black text-lg sm:text-xl">{{ $member['initials'] }}</span>
            </div>
            <p class="text-sm font-bold text-slate-800">{{ $member['name'] }}</p>
            <p class="text-xs text-slate-500 mt-0.5">{{ $member['role'] }}</p>
            <a href="{{ $member['github_url'] }}" target="_blank" rel="noopener noreferrer" class="inline-block mt-1 text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                {{ $member['github'] }}
            </a>
        </div>
        @endforeach
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MEDICAL DISCLAIMER (FULL)
══════════════════════════════════════════════════════════ --}}
<section class="bg-amber-50 border-y border-amber-200">
    <div class="mx-auto max-w-4xl px-4 py-8">
        <div class="flex gap-4 items-start">
            <div class="shrink-0 w-10 h-10 rounded-xl bg-amber-100 border border-amber-200 flex items-center justify-center text-xl">⚕️</div>
            <div>
                <p class="text-sm font-black text-amber-900 mb-2">চিকিৎসা সংক্রান্ত ডিসক্লেইমার</p>
                <div class="space-y-2 text-xs text-amber-800 leading-relaxed font-medium">
                    <p>রক্তদূত একটি স্বেচ্ছাসেবী সমন্বয় প্ল্যাটফর্ম এবং এটি কোনোভাবেই চিকিৎসা প্রতিষ্ঠান, ব্লাড ব্যাংক বা স্বাস্থ্যসেবা সংস্থা নয়।</p>
                    <p>প্ল্যাটফর্মে প্রদর্শিত কোনো তথ্যকেই চিকিৎসা পরামর্শ হিসেবে বিবেচনা করা যাবে না। রক্তগ্রহণ, ট্রান্সফিউশন বা যেকোনো চিকিৎসা সিদ্ধান্তের জন্য অবশ্যই লাইসেন্সপ্রাপ্ত চিকিৎসক ও হাসপাতালের পরামর্শ নিন।</p>
                    <p>রক্তদানের পূর্বে ডোনারকে নিজের স্বাস্থ্যগত যোগ্যতা সম্পর্কে নিশ্চিত হতে হবে। রক্তগ্রহণের পূর্বে হাসপাতালে প্রয়োজনীয় টাইপিং, স্ক্রিনিং ও ক্রস-ম্যাচিং পরীক্ষা বাধ্যতামূলক।</p>
                </div>
            </div>
        </div>
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
                🩸 ডোনার হিসেবে যোগ দিন
            </a>
            <a href="{{ route('contact.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-sm px-6 py-3 rounded-xl transition-all">
                ✉️ যোগাযোগ করুন
            </a>
        </div>
        <p class="mt-6 text-xs text-slate-500">সর্বশেষ আপডেট: ১৭ এপ্রিল, ২০২৬</p>
    </div>
</section>

@endsection
