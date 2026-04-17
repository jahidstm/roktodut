@extends('layouts.app')

@section('title', 'প্রাইভেসি পলিসি — রক্তদূত')

@section('content')

{{-- ══ Hero ══════════════════════════════════════════════════════════════ --}}
<div class="bg-gradient-to-br from-slate-800 to-slate-900">
    <div class="mx-auto max-w-5xl px-4 py-12 sm:py-14">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl">🔒</div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">আইনি দলিল</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">প্রাইভেসি পলিসি</h1>
        <p class="mt-2 text-slate-400 text-sm font-medium">সর্বশেষ আপডেট: ১৭ এপ্রিল, ২০২৬ · কার্যকর থেকে: ১৭ এপ্রিল, ২০২৬</p>
        <p class="mt-1 text-slate-500 text-xs font-medium max-w-2xl">
            রক্তদূত প্ল্যাটফর্ম ব্যবহার করলে আপনি এই প্রাইভেসি পলিসিতে সম্মতি জানাচ্ছেন বলে গণ্য হবে। অনুগ্রহ করে সম্পূর্ণ পড়ুন।
        </p>
    </div>
</div>

<div class="mx-auto max-w-5xl px-4 py-10 lg:py-12">
    <div class="lg:grid lg:grid-cols-[1fr_260px] lg:gap-12 items-start">

        {{-- ══════════════════════════ MAIN CONTENT ══════════════════════════ --}}
        <article class="min-w-0">

            {{-- Intro Notice --}}
            <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 flex gap-3 items-start">
                <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-blue-800 font-medium leading-relaxed">
                    রক্তদূত আপনার গোপনীয়তাকে সর্বোচ্চ গুরুত্ব দেয়। এই নীতিমালা ব্যাখ্যা করে আমরা কী তথ্য সংগ্রহ করি, কেন করি, এবং কীভাবে সুরক্ষিত রাখি।
                    কোনো প্রশ্ন থাকলে <a href="{{ route('contact.create') }}" class="font-bold underline hover:text-blue-600 transition-colors">যোগাযোগ করুন</a>।
                </p>
            </div>

            {{-- Mobile TOC (collapsible with Alpine) --}}
            <div class="lg:hidden mb-8 rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm"
                 x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-5 py-4 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors focus:outline-none">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        বিষয়বস্তু
                    </span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-transition class="border-t border-slate-100 px-5 py-4" style="display:none;">
                    @include('pages._privacy_toc')
                </div>
            </div>

            {{-- ───────────────────────────────────────── --}}
            {{-- Section 1: ভূমিকা --}}
            <section id="intro" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">📄</div>
                    <h2 class="text-xl font-black text-slate-900">ভূমিকা</h2>
                </div>
                <div class="prose-custom">
                    <p>রক্তদূত ("আমরা", "আমাদের", "প্ল্যাটফর্ম") বাংলাদেশে পরিচালিত একটি স্বেচ্ছাসেবী রক্তদান সমন্বয় প্ল্যাটফর্ম। এই প্রাইভেসি পলিসি ("পলিসি") ব্যাখ্যা করে যে আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ করি, ব্যবহার করি, সংরক্ষণ করি এবং কোন পরিস্থিতিতে প্রকাশ করি।</p>
                    <p>আমরা বাংলাদেশের ডিজিটাল নিরাপত্তা আইন ২০১৮ এবং ব্যক্তিগত তথ্য সুরক্ষার প্রচলিত সর্বোত্তম অনুশীলন মেনে চলার চেষ্টা করি। এই পলিসি শুধুমাত্র রক্তদূতের ওয়েবসাইট ও মোবাইল সেবার ক্ষেত্রে প্রযোজ্য।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 2: তথ্য সংগ্রহ --}}
            <section id="data-collection" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-base">📋</div>
                    <h2 class="text-xl font-black text-slate-900">আমরা কী তথ্য সংগ্রহ করি</h2>
                </div>
                <div class="prose-custom">
                    <p>আপনি যখন রক্তদূতে নিবন্ধন করেন বা সেবা ব্যবহার করেন, তখন নিচের ধরনের তথ্য সংগ্রহ ও প্রক্রিয়া করা হয়:</p>
                </div>
                <div class="mt-4 space-y-3">
                    @php
                    $dataTypes = [
                        [
                            'icon'  => '👤',
                            'title' => 'পরিচয়গত তথ্য',
                            'items' => 'পূর্ণ নাম, জন্মতারিখ, লিঙ্গ, রক্তের গ্রুপ, ঠিকানা (জেলা ও উপজেলা স্তর পর্যন্ত)',
                            'color' => 'bg-blue-50 border-blue-100',
                        ],
                        [
                            'icon'  => '📞',
                            'title' => 'যোগাযোগ তথ্য',
                            'items' => 'ফোন নম্বর (ডিফল্টভাবে মাস্কড, সীমিত শর্তে প্রকাশিত) এবং ইমেইল ঠিকানা',
                            'color' => 'bg-emerald-50 border-emerald-100',
                        ],
                        [
                            'icon'  => '🪪',
                            'title' => 'পরিচয়পত্র (ঐচ্ছিক — শুধু ভেরিফিকেশনের জন্য)',
                            'items' => 'জাতীয় পরিচয়পত্র (NID) নম্বর ও স্ক্যান কপি। ভেরিফাইড ব্যাজের জন্য আপলোড করতে হয়, বাধ্যতামূলক নয়।',
                            'color' => 'bg-amber-50 border-amber-100',
                        ],
                        [
                            'icon'  => '🩸',
                            'title' => 'স্বাস্থ্য সংক্রান্ত তথ্য',
                            'items' => 'রক্তদানের ইতিহাস, সর্বশেষ দানের তারিখ, দানের মোট সংখ্যা (স্ব-প্রদত্ত)',
                            'color' => 'bg-red-50 border-red-100',
                        ],
                        [
                            'icon'  => '📊',
                            'title' => 'ব্যবহার তথ্য',
                            'items' => 'লগইন সময়, পেজ ভিজিট, সার্চ কোয়েরি — সাধারণত অ্যানোনিমাস বা অ্যাগ্রিগেটেড ফর্মে',
                            'color' => 'bg-slate-50 border-slate-200',
                        ],
                        [
                            'icon'  => '🔗',
                            'title' => 'তৃতীয় পক্ষের OAuth তথ্য',
                            'items' => 'Google বা Facebook দিয়ে লগইন করলে সংশ্লিষ্ট সেবা থেকে নাম, ইমেইল ও প্রোফাইল ছবি পাওয়া যায়',
                            'color' => 'bg-purple-50 border-purple-100',
                        ],
                    ];
                    @endphp
                    @foreach($dataTypes as $dt)
                    <div class="flex items-start gap-3 rounded-xl border {{ $dt['color'] }} p-4">
                        <span class="text-xl shrink-0">{{ $dt['icon'] }}</span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $dt['title'] }}</p>
                            <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $dt['items'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 3: তথ্য ব্যবহার --}}
            <section id="data-use" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-base">🎯</div>
                    <h2 class="text-xl font-black text-slate-900">তথ্য ব্যবহারের উদ্দেশ্য</h2>
                </div>
                <div class="prose-custom">
                    <p>সংগৃহীত তথ্য কেবলমাত্র নিচের সুনির্দিষ্ট উদ্দেশ্যে ব্যবহার করা হয়:</p>
                    <ul>
                        <li>রক্তের গ্রুপ ও লোকেশন অনুযায়ী ডোনার ও অনুরোধকারীর মধ্যে সংযোগ স্থাপন</li>
                        <li>ডোনারের পরিচয় যাচাই (NID ভেরিফিকেশন, ম্যানুয়াল প্রক্রিয়া)</li>
                        <li>রিয়েল-টাইম রক্তের অনুরোধ নোটিফিকেশন ও অ্যালার্ট পাঠানো</li>
                        <li>গ্যামিফিকেশন পয়েন্ট, ব্যাজ ও লিডারবোর্ড ব্যবস্থাপনা</li>
                        <li>প্ল্যাটফর্মের নিরাপত্তা নিশ্চিত করা ও অপব্যবহার প্রতিরোধ</li>
                        <li>প্ল্যাটফর্ম উন্নয়নের জন্য অ্যানোনিমাস পরিসংখ্যান বিশ্লেষণ</li>
                        <li>আইনি দায়িত্ব পালন ও বিরোধ নিষ্পত্তিতে সহায়তা</li>
                    </ul>
                    <p class="text-sm font-semibold text-slate-700 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 mt-4">
                        ✅ আমরা আপনার ব্যক্তিগত তথ্য কখনো তৃতীয় পক্ষের কাছে বিক্রি করি না বা বিজ্ঞাপনের উদ্দেশ্যে শেয়ার করি না।
                    </p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 4: ফোন নম্বর প্রাইভেসি --}}
            <section id="phone-reveal" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-base">📞</div>
                    <h2 class="text-xl font-black text-slate-900">ফোন নম্বর প্রাইভেসি</h2>
                </div>
                <div class="prose-custom">
                    <p>ডোনারের ফোন নম্বর ডিফল্টভাবে মাস্কড (লুকানো) থাকে এবং কেবল নিচের শর্তে প্রকাশিত হয়:</p>
                    <ul>
                        <li>রক্তের অনুরোধকারী OTP চ্যালেঞ্জ সফলভাবে পাস করলে</li>
                        <li>প্রতি ২৪ ঘণ্টায় একটি অ্যাকাউন্ট থেকে সর্বোচ্চ নির্দিষ্ট সংখ্যক নম্বর দেখার সুযোগ (রেট লিমিট প্রযোজ্য)</li>
                        <li>রেট লিমিট অতিক্রম করলে অনুরোধ ব্লক হয় এবং লগে সংরক্ষিত হয়</li>
                    </ul>
                    <p>প্রতিটি নম্বর-প্রকাশ ঘটনা সিস্টেমে লগ করা হয়। অপব্যবহার শনাক্ত হলে অ্যাকাউন্ট স্বয়ংক্রিয়ভাবে পর্যালোচনায় পাঠানো হয়।</p>
                </div>
                <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 px-5 py-4">
                    <p class="text-sm font-bold text-orange-800 mb-1">⚠️ গুরুত্বপূর্ণ</p>
                    <p class="text-xs text-orange-700 leading-relaxed">ডোনারের নম্বর ব্যবহার করে হয়রানি, স্প্যাম বা অনাকাঙ্ক্ষিত যোগাযোগ করা সম্পূর্ণ নিষিদ্ধ। এই নিয়ম লঙ্ঘনে অ্যাকাউন্ট স্থায়ীভাবে নিষিদ্ধ এবং প্রযোজ্য ক্ষেত্রে আইনি ব্যবস্থা নেওয়া হবে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 5: NID ডকুমেন্ট সুরক্ষা --}}
            <section id="nid-security" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">🪪</div>
                    <h2 class="text-xl font-black text-slate-900">NID ডকুমেন্ট সুরক্ষা</h2>
                </div>
                <div class="prose-custom">
                    <p>আপলোড করা জাতীয় পরিচয়পত্র (NID) ডকুমেন্টের ক্ষেত্রে আমাদের নিরাপত্তা ব্যবস্থা নিচে বর্ণিত:</p>
                    <ul>
                        <li>ফাইলগুলো প্রাইভেট স্টোরেজে সংরক্ষিত — সরাসরি পাবলিক URL নেই</li>
                        <li>শুধুমাত্র অ্যাডমিন রোলধারী ব্যক্তি ভেরিফিকেশনের উদ্দেশ্যে অ্যাক্সেস করতে পারেন</li>
                        <li>অ্যাডমিন অ্যাক্সেস লগে সংরক্ষিত হয়</li>
                        <li>ভেরিফিকেশন সম্পন্ন হলে ডোনার অনুরোধ করলে ডকুমেন্ট মুছে ফেলা যাবে</li>
                        <li>ডেটা রিটেনশন নীতি: অ্যাকাউন্ট সক্রিয় থাকলে সর্বোচ্চ ১ বছর; অ্যাকাউন্ট ডিলিটে সাথে সাথে মুছে যাবে</li>
                        <li>NID স্ক্যান কখনো তৃতীয় পক্ষের সাথে শেয়ার করা হয় না</li>
                    </ul>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-5 py-4">
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                        <strong>দ্রষ্টব্য:</strong> NID ভেরিফিকেশন একটি ঐচ্ছিক সেবা। ভেরিফিকেশন ছাড়াও রক্তদাতা হিসেবে নিবন্ধন করা সম্ভব — তবে ভেরিফাইড ব্যাজ ও সংশ্লিষ্ট বিশেষ সুবিধা পাওয়া যাবে না।
                    </p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 6: কুকিজ ও ট্র্যাকিং --}}
            <section id="cookies" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-base">🍪</div>
                    <h2 class="text-xl font-black text-slate-900">কুকিজ ও সেশন</h2>
                </div>
                <div class="prose-custom">
                    <p>রক্তদূত কেবল প্রয়োজনীয় ও কার্যকরী কুকিজ ব্যবহার করে:</p>
                    <ul>
                        <li><strong>সেশন কুকি:</strong> লগইন অবস্থা বজায় রাখতে (আবশ্যক, ব্রাউজার বন্ধে মুছে যায়)</li>
                        <li><strong>CSRF টোকেন:</strong> ফর্ম জমা দেওয়ার সময় নিরাপত্তা নিশ্চিত করতে (আবশ্যক)</li>
                        <li><strong>পছন্দ কুকি:</strong> ভাষা বা থিম সেটিংস সংরক্ষণে (ঐচ্ছিক)</li>
                    </ul>
                    <p>আমরা কোনো তৃতীয় পক্ষের বিজ্ঞাপন ট্র্যাকিং কুকিজ, ক্রস-সাইট ট্র্যাকিং বা ফিঙ্গারপ্রিন্টিং প্রযুক্তি ব্যবহার করি না। ব্রাউজারের কুকি সেটিংস পরিবর্তন করলে প্ল্যাটফর্মের কিছু কার্যকারিতা সীমিত হতে পারে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 7: আপনার অধিকার --}}
            <section id="user-rights" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-base">⚖️</div>
                    <h2 class="text-xl font-black text-slate-900">আপনার অধিকার</h2>
                </div>
                <div class="prose-custom mb-4">
                    <p>রক্তদূতে নিবন্ধিত প্রতিটি ব্যবহারকারী নিচের অধিকার প্রয়োগ করতে পারেন:</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                    $rights = [
                        ['icon' => '👁️', 'title' => 'তথ্য দেখার অধিকার',  'desc' => 'আপনার সম্পর্কে কী তথ্য সংরক্ষিত আছে তা প্রোফাইল পেজ থেকে দেখতে পারবেন।'],
                        ['icon' => '✏️', 'title' => 'সংশোধনের অধিকার',    'desc' => 'ভুল বা পুরনো তথ্য প্রোফাইল সেটিংস থেকে সরাসরি সংশোধন করুন। গুরুত্বপূর্ণ তথ্যের জন্য যোগাযোগ করুন।'],
                        ['icon' => '🗑️', 'title' => 'মুছে ফেলার অধিকার',  'desc' => 'অ্যাকাউন্ট ডিলিট করলে আপনার ব্যক্তিগত তথ্য সিস্টেম থেকে সরিয়ে ফেলা হবে (লিগ্যাল লগ ব্যতীত)।'],
                        ['icon' => '📤', 'title' => 'তথ্য পোর্টেবিলিটি',  'desc' => 'আপনার ডেটার একটি কপি চাইতে পারেন। অনুরোধের ৭ কার্যদিবসের মধ্যে ইমেইলে পাঠানো হবে।'],
                        ['icon' => '🚫', 'title' => 'প্রক্রিয়ার আপত্তি',  'desc' => 'অতিরিক্ত বা অপ্রয়োজনীয় তথ্য প্রক্রিয়া বন্ধ করার অনুরোধ করতে পারেন।'],
                        ['icon' => '📬', 'title' => 'অভিযোগ দাখিল',      'desc' => 'গোপনীয়তা লঙ্ঘনের অভিযোগ সরাসরি privacy@roktodut.com ঠিকানায় পাঠান।'],
                    ];
                    @endphp
                    @foreach($rights as $r)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 flex gap-3 items-start shadow-sm">
                        <span class="text-lg shrink-0">{{ $r['icon'] }}</span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $r['title'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $r['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 8: পলিসি আপডেট --}}
            <section id="policy-updates" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-base">🔄</div>
                    <h2 class="text-xl font-black text-slate-900">নীতিমালা আপডেট</h2>
                </div>
                <div class="prose-custom">
                    <p>রক্তদূত যেকোনো সময় এই প্রাইভেসি পলিসি আপডেট করার অধিকার রাখে। গুরুত্বপূর্ণ পরিবর্তন হলে নিবন্ধিত ইমেইলে বা প্ল্যাটফর্মের ইন-অ্যাপ নোটিফিকেশনের মাধ্যমে অবহিত করা হবে।</p>
                    <p>পরিবর্তনের পরেও প্ল্যাটফর্ম ব্যবহার অব্যাহত রাখলে আপনি হালনাগাদ পলিসিতে সম্মতি দিয়েছেন বলে গণ্য হবে। নিয়মিত এই পৃষ্ঠাটি পরীক্ষা করার পরামর্শ দেওয়া হচ্ছে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 9: যোগাযোগ --}}
            <section id="contact-us" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-base">✉️</div>
                    <h2 class="text-xl font-black text-slate-900">যোগাযোগ করুন</h2>
                </div>
                <div class="prose-custom">
                    <p>প্রাইভেসি সংক্রান্ত যেকোনো প্রশ্ন, অভিযোগ বা তথ্য সংশোধনের অনুরোধের জন্য:</p>
                </div>
                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 flex flex-col sm:flex-row gap-4 items-start">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-800 mb-1">রক্তদূত প্রাইভেসি টিম</p>
                        <p class="text-sm text-slate-600">ইমেইল: <a href="mailto:privacy@roktodut.com" class="text-red-600 font-semibold hover:underline">privacy@roktodut.com</a></p>
                        <p class="text-sm text-slate-600 mt-0.5">সাড়া দেওয়ার সময়: ৭২ ঘণ্টার মধ্যে (কর্মদিবস)</p>
                    </div>
                    <a href="{{ route('contact.create') }}"
                       class="shrink-0 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                        📬 বার্তা পাঠান
                    </a>
                </div>
            </section>

        </article>

        {{-- ══════════════════════════ STICKY TOC (desktop) ══════════════════ --}}
        <aside class="hidden lg:block">
            <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <p class="text-xs font-black text-slate-700 uppercase tracking-widest">বিষয়বস্তু</p>
                </div>
                <nav class="px-4 py-4">
                    @include('pages._privacy_toc')
                </nav>
            </div>
        </aside>

    </div>
</div>

{{-- Custom prose styles (inline, no @tailwindcss/typography dependency needed) --}}
@push('head')
<style>
.prose-custom p      { @apply text-sm text-slate-600 leading-relaxed mb-3 font-medium; }
.prose-custom ul     { @apply text-sm text-slate-600 space-y-1.5 list-none pl-0 mb-3; }
.prose-custom ul li  { @apply flex items-start gap-2; }
.prose-custom ul li::before { content: '›'; @apply text-red-500 font-black shrink-0; }
.prose-custom strong { @apply font-bold text-slate-700; }
</style>
@endpush

@endsection
