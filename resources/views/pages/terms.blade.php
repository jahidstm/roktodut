@extends('layouts.app')

@section('title', 'ব্যবহারের শর্তাবলী — রক্তদূত')

@section('content')

{{-- ══ Hero ══════════════════════════════════════════════════════════════ --}}
<div class="bg-gradient-to-br from-slate-800 to-slate-900">
    <div class="mx-auto max-w-5xl px-4 py-12 sm:py-14">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl">📜</div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">আইনি দলিল</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">ব্যবহারের শর্তাবলী</h1>
        <p class="mt-2 text-slate-400 text-sm font-medium">সর্বশেষ আপডেট: ১৭ এপ্রিল, ২০২৬ · রক্তদূত প্ল্যাটফর্ম</p>
        <p class="mt-1 text-slate-500 text-xs font-medium max-w-2xl">
            প্ল্যাটফর্ম ব্যবহার শুরু করার আগে এই শর্তাবলী মনোযোগ দিয়ে পড়ুন।
        </p>
    </div>
</div>

<div class="mx-auto max-w-5xl px-4 py-10 lg:py-12">
    <div class="lg:grid lg:grid-cols-[1fr_260px] lg:gap-12 items-start">

        {{-- ══════════════════════════ MAIN CONTENT ══════════════════════════ --}}
        <article class="min-w-0">

            {{-- Intro Notice --}}
            <div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 flex gap-3 items-start">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <p class="text-sm text-amber-800 font-medium leading-relaxed">
                    রক্তদূত প্ল্যাটফর্মে অ্যাকাউন্ট তৈরি করে বা যেকোনো সেবা ব্যবহার করে আপনি এই শর্তাবলীতে সম্মতি দিচ্ছেন বলে গণ্য হবে। সম্মত না হলে প্ল্যাটফর্ম ব্যবহার বন্ধ করুন।
                </p>
            </div>

            {{-- Mobile TOC --}}
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
                    @include('pages._terms_toc')
                </div>
            </div>

            {{-- ───────────────────────────────────────── --}}
            {{-- Section 1: প্ল্যাটফর্মের পরিচয় --}}
            <section id="platform-nature" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">🩺</div>
                    <h2 class="text-xl font-black text-slate-900">রক্তদূত কী এবং কী নয়</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদূত একটি <strong>স্বেচ্ছাসেবী রক্তদান সমন্বয় প্ল্যাটফর্ম</strong>। এটি কেবল রক্তদাতা ও রক্তগ্রহীতার মধ্যে সংযোগ তৈরি করে। রক্তদূত কোনোভাবেই নয়:</p>
                    <ul>
                        <li>কোনো চিকিৎসা প্রতিষ্ঠান, হাসপাতাল বা ব্লাড ব্যাংক</li>
                        <li>কোনো সরকারি স্বাস্থ্যসেবা সংস্থা বা তার প্রতিনিধি</li>
                        <li>চিকিৎসা পরামর্শ, রোগ নির্ণয় বা চিকিৎসার পরামর্শদাতা</li>
                    </ul>
                </div>
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-5 py-4">
                    <p class="text-sm font-bold text-red-800 mb-1">⚕️ চিকিৎসা সংক্রান্ত ডিসক্লেইমার</p>
                    <p class="text-xs text-red-700 leading-relaxed">রক্তদূতের মাধ্যমে ডোনারের সাথে যোগাযোগ করলেও রক্ত গ্রহণের আগে হাসপাতালে <strong>টাইপিং, স্ক্রিনিং ও ক্রস-ম্যাচিং বাধ্যতামূলক</strong>। এই দায়িত্ব সম্পূর্ণরূপে রক্তগ্রহীতা ও সংশ্লিষ্ট হাসপাতালের। রক্তদূত এই প্রক্রিয়ার পরিণতির জন্য দায়ী নয়।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 2: শর্ত গ্রহণ --}}
            <section id="acceptance" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-base">✅</div>
                    <h2 class="text-xl font-black text-slate-900">শর্ত গ্রহণ ও যোগ্যতা</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদূত ব্যবহার করতে হলে আপনাকে:</p>
                    <ul>
                        <li>কমপক্ষে ১৮ বছর বয়সী হতে হবে</li>
                        <li>বাংলাদেশের নাগরিক বা বৈধ বাসিন্দা হতে হবে</li>
                        <li>একটি বৈধ ইমেইল ঠিকানা ও ফোন নম্বর দিয়ে নিবন্ধন করতে হবে</li>
                        <li>প্রদত্ত সকল তথ্য সত্য, সঠিক ও হালনাগাদ রাখতে হবে</li>
                    </ul>
                    <p>আমরা যেকোনো সময় এই শর্ত আপডেট করতে পারি। পরিবর্তন হলে ইমেইল বা ইন-অ্যাপ নোটিফিকেশনের মাধ্যমে অবহিত করা হবে। পরিবর্তনের পরেও প্ল্যাটফর্ম ব্যবহার অব্যাহত রাখলে আপনি হালনাগাদ শর্তে সম্মতি দিয়েছেন বলে গণ্য হবে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 3: অ্যাকাউন্ট দায়িত্ব --}}
            <section id="account" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-base">🔐</div>
                    <h2 class="text-xl font-black text-slate-900">অ্যাকাউন্ট দায়িত্ব</h2>
                </div>
                <div class="prose-terms">
                    <p>আপনার অ্যাকাউন্ট সম্পূর্ণ আপনার দায়িত্বে। এর মধ্যে অন্তর্ভুক্ত:</p>
                    <ul>
                        <li>পাসওয়ার্ড গোপন রাখা ও নিরাপদভাবে সংরক্ষণ করা</li>
                        <li>আপনার অ্যাকাউন্টে সংঘটিত যেকোনো কার্যক্রমের দায়িত্ব</li>
                        <li>অ্যাকাউন্ট হ্যাক বা অননুমোদিত প্রবেশের সন্দেহ হলে তাৎক্ষণিক রিপোর্ট করা</li>
                        <li>একটি ইমেইলে একটিমাত্র অ্যাকাউন্ট রাখা (duplicate account নিষিদ্ধ)</li>
                        <li>অ্যাকাউন্ট অন্য কাউকে হস্তান্তর বা শেয়ার না করা</li>
                    </ul>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 4: ডোনারের দায়িত্ব --}}
            <section id="donor-rules" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">🩸</div>
                    <h2 class="text-xl font-black text-slate-900">ডোনারের দায়িত্ব</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদাতা হিসেবে নিবন্ধন করলে আপনি নিশ্চিত করছেন যে:</p>
                    <ul>
                        <li>আপনার স্বাস্থ্য রক্তদানের জন্য যোগ্য এবং আপনি এ বিষয়ে সৎ তথ্য দিয়েছেন</li>
                        <li>সর্বশেষ রক্তদানের পর কমপক্ষে ১২০ দিন (পুরুষ) বা ১৫০ দিন (মহিলা) অতিবাহিত হয়েছে</li>
                        <li>আপনি কোনো সংক্রামক রোগ, রক্তবাহিত রোগ বা উচ্চঝুঁকিপূর্ণ স্বাস্থ্যাবস্থায় নেই</li>
                        <li>আপনার প্রোফাইলে সঠিক রক্তের গ্রুপ, লোকেশন ও উপলব্ধতা উল্লেখ করা হয়েছে</li>
                        <li>অনুরোধে সাড়া না দিতে পারলে সিস্টেমে অনুপলব্ধ স্ট্যাটাস আপডেট করবেন</li>
                    </ul>
                </div>
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4">
                    <p class="text-xs text-amber-800 leading-relaxed font-medium">
                        <strong>দ্রষ্টব্য:</strong> রক্তদানের উপযুক্ততা নির্ধারণের চূড়ান্ত দায়িত্ব ডোনারের নিজের এবং রক্তদানের দিন হাসপাতালের ডাক্তারের। রক্তদূত এই সিদ্ধান্তে হস্তক্ষেপ করে না।
                    </p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 5: রক্তগ্রহীতার দায়িত্ব --}}
            <section id="recipient-rules" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-base">🏥</div>
                    <h2 class="text-xl font-black text-slate-900">রক্তগ্রহীতার দায়িত্ব</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তের অনুরোধ জানানোর সময় অনুরোধকারী নিশ্চিত করছেন যে:</p>
                    <ul>
                        <li>অনুরোধটি বাস্তব এবং সত্যিকারের চিকিৎসা প্রয়োজনেই করা হচ্ছে</li>
                        <li>রক্ত গ্রহণের আগে হাসপাতালে যথাযথ ব্লাড টাইপিং, স্ক্রিনিং ও ক্রস-ম্যাচিং পরীক্ষা করানো হবে</li>
                        <li>হাসপাতালের নিয়ম ও রক্তদান প্রোটোকল মানা হবে</li>
                        <li>ডোনারের সাথে যোগাযোগ কেবল রক্তদানের উদ্দেশ্যে করা হবে, হয়রানিমূলক নয়</li>
                        <li>ডোনারের নম্বর reveal করা হলে সেটি শুধু এই নির্দিষ্ট অনুরোধের জন্য ব্যবহার করা হবে</li>
                    </ul>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 6: নিষিদ্ধ কার্যক্রম --}}
            <section id="prohibited" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-base">🚫</div>
                    <h2 class="text-xl font-black text-slate-900">নিষিদ্ধ কার্যক্রম</h2>
                </div>
                <div class="prose-terms">
                    <p>নিচের কার্যক্রম সম্পূর্ণ নিষিদ্ধ এবং লঙ্ঘনে তাৎক্ষণিক ব্যবস্থা নেওয়া হবে:</p>
                </div>
                <div class="mt-4 space-y-2">
                    @php
                    $prohibited = [
                        ['icon' => '💰', 'title' => 'রক্ত বিক্রয়',           'text' => 'রক্তের বিনিময়ে অর্থ, উপহার বা কোনো সুবিধা গ্রহণ বা দাবি করা। রক্তদান স্বেচ্ছামূলক ও বিনামূল্যে।'],
                        ['icon' => '📞', 'title' => 'হয়রানি ও স্প্যাম',     'text' => 'ডোনারের নম্বর ব্যবহার করে রক্তদানের বাইরে যোগাযোগ, হয়রানি, বারবার ফোন বা স্প্যাম মেসেজ।'],
                        ['icon' => '🤖', 'title' => 'অটোমেটেড স্ক্র্যাপিং', 'text' => 'বট, স্ক্রিপ্ট, ক্রলার বা যেকোনো অটোমেশন দিয়ে পরিচয়পত্র বা তথ্য সংগ্রহ করা।'],
                        ['icon' => '🎭', 'title' => 'ভুয়া পরিচয়',          'text' => 'মিথ্যা নাম, NID বা অন্যের পরিচয় ব্যবহার করে নিবন্ধন করা বা অনুরোধ পাঠানো।'],
                        ['icon' => '🛡️', 'title' => 'মিথ্যা অনুরোধ',       'text' => 'পরীক্ষামূলক, বিনোদনমূলক বা উদ্দেশ্যহীন ভুয়া রক্তের অনুরোধ তৈরি করা।'],
                        ['icon' => '🔓', 'title' => 'সিস্টেম অপব্যবহার',    'text' => 'প্ল্যাটফর্মের নিরাপত্তা ভাঙার চেষ্টা, রেট লিমিট বাইপাস, বা একাধিক অ্যাকাউন্ট তৈরি।'],
                        ['icon' => '📢', 'title' => 'বিজ্ঞাপন ও পণ্য প্রচার', 'text' => 'রক্তদান-সম্পর্কহীন পণ্য, সেবা বা সংগঠনের বিজ্ঞাপন প্রচারের জন্য প্ল্যাটফর্ম ব্যবহার।'],
                    ];
                    @endphp
                    @foreach($prohibited as $p)
                    <div class="flex items-start gap-3 rounded-xl border border-rose-100 bg-rose-50/60 px-4 py-3">
                        <span class="text-base shrink-0 mt-0.5">{{ $p['icon'] }}</span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $p['title'] }}</p>
                            <p class="text-xs text-slate-600 leading-relaxed mt-0.5">{{ $p['text'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 7: অ্যাকাউন্ট সাসপেনশন ও ব্যান --}}
            <section id="termination" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-base">🔚</div>
                    <h2 class="text-xl font-black text-slate-900">অ্যাকাউন্ট সাসপেনশন ও স্থায়ী ব্যান</h2>
                </div>
                <div class="prose-terms">
                    <p>নিচের ক্ষেত্রে আমরা পূর্ববর্তী নোটিশ ছাড়াই অ্যাকাউন্ট ব্যবস্থা নিতে পারি:</p>
                </div>
                <div class="mt-4 space-y-3">
                    @php
                    $policies = [
                        ['level' => 'সতর্কতা', 'color' => 'bg-yellow-50 border-yellow-200 text-yellow-800', 'badge' => 'bg-yellow-100 text-yellow-700',
                         'desc' => 'প্রথমবার ছোট লঙ্ঘন (যেমন: বারবার মিথ্যা উপলব্ধতা)। রেকর্ড সংরক্ষিত হবে।'],
                        ['level' => 'সাময়িক স্থগিত', 'color' => 'bg-orange-50 border-orange-200 text-orange-800', 'badge' => 'bg-orange-100 text-orange-700',
                         'desc' => 'একাধিক রিপোর্ট, রেট লিমিট বারবার অতিক্রম, বা যাচাইয়ের পর মাঝারি লঙ্ঘনে ৭–৩০ দিনের জন্য স্থগিত।'],
                        ['level' => 'স্থায়ী নিষিদ্ধ', 'color' => 'bg-red-50 border-red-200 text-red-800', 'badge' => 'bg-red-100 text-red-700',
                         'desc' => 'গুরুতর লঙ্ঘন: হয়রানি, রক্ত বিক্রয়, জাল NID, সিস্টেম হ্যাক বা বারবার একই লঙ্ঘনে স্থায়ী ব্যান এবং প্রযোজ্য ক্ষেত্রে আইনি পদক্ষেপ।'],
                    ];
                    @endphp
                    @foreach($policies as $policy)
                    <div class="rounded-xl border {{ $policy['color'] }} px-4 py-3 flex gap-3 items-start">
                        <span class="shrink-0 text-xs font-black px-2 py-0.5 rounded-full {{ $policy['badge'] }}">{{ $policy['level'] }}</span>
                        <p class="text-xs leading-relaxed font-medium">{{ $policy['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="prose-terms mt-4">
                    <p>আপনি নিজেও যেকোনো সময় প্রোফাইল সেটিংস থেকে অ্যাকাউন্ট ডিলিট করতে পারবেন। নিষিদ্ধ অ্যাকাউন্টের বিরুদ্ধে আপিলের সুযোগ দেওয়া হয় — আপিল করতে <a href="{{ route('contact.create') }}" class="text-red-600 font-semibold hover:underline">যোগাযোগ করুন</a>।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 8: দায়বদ্ধতার সীমা --}}
            <section id="liability" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-base">⚖️</div>
                    <h2 class="text-xl font-black text-slate-900">দায়বদ্ধতার সীমা</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদূত একটি সমন্বয় মাধ্যম মাত্র। সহজ ভাষায়, আমরা যা নিশ্চিত করতে পারি না তার জন্য আমরা দায়ী নই:</p>
                    <ul>
                        <li>কোনো ডোনারের সরবরাহকৃত রক্তের মান বা নিরাপত্তা যাচাই করা আমাদের পক্ষে সম্ভব নয় — এটি হাসপাতালের দায়িত্ব</li>
                        <li>ডোনার ও গ্রহীতার মধ্যে রক্তদানের বাইরে যেকোনো ব্যক্তিগত বিরোধের দায় আমাদের নয়</li>
                        <li>ডোনার স্বেচ্ছায় সাড়া না দিলে বা সময়মতো না আসলে তার দায় রক্তদূতের নয়</li>
                        <li>তৃতীয় পক্ষের (হাসপাতাল, ক্লাব, অর্গানাইজেশন) কার্যক্রমের দায় আমরা বহন করব না</li>
                        <li>প্রযুক্তিগত সমস্যা, সার্ভার ডাউনটাইম বা ডেটা লসের ক্ষেত্রে দায় সীমিত এবং পরিষেবার পুনরুদ্ধারে আমরা সর্বোচ্চ সচেষ্ট থাকব</li>
                    </ul>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-5 py-4">
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                        <strong>জরুরি নোট:</strong> জরুরি রক্তের জন্য সরকারি হাসপাতালের ব্লাড ব্যাংক এবং জাতীয় হেল্পলাইন (১৬৪৩০) ব্যবহার করুন। রক্তদূত একটি সম্পূরক স্বেচ্ছাসেবী সেবা, এটি জরুরি সেবার বিকল্প নয়।
                    </p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 9: প্রযোজ্য আইন --}}
            <section id="governing-law" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-base">🏛️</div>
                    <h2 class="text-xl font-black text-slate-900">প্রযোজ্য আইন</h2>
                </div>
                <div class="prose-terms">
                    <p>এই শর্তাবলী বাংলাদেশের প্রচলিত আইন দ্বারা পরিচালিত হবে। যেকোনো বিরোধ বা দাবি নিষ্পত্তির জন্য ঢাকার উপযুক্ত আদালত এখতিয়ার রাখে।</p>
                    <p>প্রযোজ্য আইনসমূহ (প্রাসঙ্গিক ক্ষেত্রে): ডিজিটাল নিরাপত্তা আইন ২০১৮; তথ্য ও যোগাযোগ প্রযুক্তি আইন ২০০৬ (সংশোধিত); ভোক্তা অধিকার সংরক্ষণ আইন ২০০৯।</p>
                </div>
            </section>

            {{-- Contact CTA --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-800">শর্তাবলী নিয়ে প্রশ্ন আছে?</p>
                    <p class="text-xs text-slate-500 mt-0.5">আমাদের দল ৪৮ ঘণ্টার মধ্যে উত্তর দেবে।</p>
                    <p class="text-xs text-slate-400 mt-1">সর্বশেষ আপডেট: ১৭ এপ্রিল, ২০২৬</p>
                </div>
                <a href="{{ route('contact.create') }}"
                   class="shrink-0 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                    ✉️ যোগাযোগ করুন
                </a>
            </div>

        </article>

        {{-- ══════════════════════════ STICKY TOC (desktop) ══════════════════ --}}
        <aside class="hidden lg:block">
            <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                    <p class="text-xs font-black text-slate-700 uppercase tracking-widest">বিষয়বস্তু</p>
                </div>
                <nav class="px-4 py-4">
                    @include('pages._terms_toc')
                </nav>
            </div>
        </aside>

    </div>
</div>

@push('head')
<style>
.prose-terms p      { @apply text-sm text-slate-600 leading-relaxed mb-3 font-medium; }
.prose-terms ul     { @apply text-sm text-slate-600 space-y-1.5 list-none pl-0 mb-3; }
.prose-terms ul li  { @apply flex items-start gap-2; }
.prose-terms ul li::before { content: '›'; @apply text-red-500 font-black shrink-0; }
.prose-terms strong { @apply font-bold text-slate-700; }
.prose-terms a      { @apply text-red-600 hover:underline; }
</style>
@endpush

@endsection
