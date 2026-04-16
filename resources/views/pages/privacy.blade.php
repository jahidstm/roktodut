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
        <p class="mt-2 text-slate-400 text-sm font-medium">সর্বশেষ আপডেট: ১৬ এপ্রিল, ২০২৬ · কার্যকর থেকে: ১ জানুয়ারি, ২০২৬</p>
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
                    রক্তদূত প্ল্যাটফর্ম ব্যবহার করে আপনি এই প্রাইভেসি পলিসিতে সম্মতি জানাচ্ছেন। কোনো প্রশ্ন থাকলে <a href="{{ route('contact.create') }}" class="font-bold underline hover:text-blue-600 transition-colors">যোগাযোগ করুন</a>।
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
            {{-- Section 1 --}}
            <section id="intro" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">📄</div>
                    <h2 class="text-xl font-black text-slate-900">ভূমিকা</h2>
                </div>
                <div class="prose-custom">
                    <p>রক্তদূত ("আমরা", "আমাদের") বাংলাদেশে পরিচালিত একটি রক্তদান সহায়তা প্ল্যাটফর্ম। এই পলিসি ব্যাখ্যা করে যে আমরা কীভাবে আপনার ব্যক্তিগত তথ্য সংগ্রহ করি, ব্যবহার করি, এবং সুরক্ষিত রাখি।</p>
                    <p>আমরা বাংলাদেশের ডিজিটাল নিরাপত্তা আইন ২০১৮ এবং ব্যক্তিগত তথ্য সুরক্ষার প্রচলিত আন্তর্জাতিক মান মেনে চলি।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 2 --}}
            <section id="data-collection" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-base">📋</div>
                    <h2 class="text-xl font-black text-slate-900">আমরা কী তথ্য সংগ্রহ করি</h2>
                </div>
                <div class="prose-custom">
                    <p>নিচের ধরনের তথ্য আমরা সংগ্রহ ও প্রক্রিয়া করি:</p>
                </div>
                <div class="mt-4 space-y-3">
                    @php
                    $dataTypes = [
                        ['icon' => '👤', 'title' => 'পরিচয়গত তথ্য', 'items' => 'নাম, জন্মতারিখ, রক্তের গ্রুপ, ঠিকানা (জেলা/উপজেলা স্তর)', 'color' => 'bg-blue-50 border-blue-100'],
                        ['icon' => '📞', 'title' => 'যোগাযোগ তথ্য', 'items' => 'ফোন নম্বর, ইমেইল ঠিকানা', 'color' => 'bg-emerald-50 border-emerald-100'],
                        ['icon' => '🪪', 'title' => 'পরিচয়পত্র (ঐচ্ছিক)', 'items' => 'NID নম্বর ও স্ক্যান কপি — শুধুমাত্র ভেরিফিকেশনের জন্য', 'color' => 'bg-amber-50 border-amber-100'],
                        ['icon' => '🩸', 'title' => 'স্বাস্থ্য সংক্রান্ত', 'items' => 'রক্তদানের ইতিহাস, সর্বশেষ দানের তারিখ', 'color' => 'bg-red-50 border-red-100'],
                        ['icon' => '📊', 'title' => 'ব্যবহার তথ্য', 'items' => 'লগইন সময়, পেজ ভিজিট, সার্চ কোয়েরি (অ্যানোনিমাস)', 'color' => 'bg-slate-50 border-slate-200'],
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

            {{-- Section 3 --}}
            <section id="data-use" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-base">🎯</div>
                    <h2 class="text-xl font-black text-slate-900">তথ্য ব্যবহারের উদ্দেশ্য</h2>
                </div>
                <div class="prose-custom">
                    <p>আপনার তথ্য নিচের উদ্দেশ্যে ব্যবহার করা হয়:</p>
                    <ul>
                        <li>ডোনার ও রক্তের অনুরোধকারীর মধ্যে সংযোগ স্থাপন</li>
                        <li>ডোনারের পরিচয় যাচাই (NID Verification)</li>
                        <li>রিয়েল-টাইম রক্তের অনুরোধ নোটিফিকেশন পাঠানো</li>
                        <li>গ্যামিফিকেশন পয়েন্ট ও ব্যাজ ব্যবস্থাপনা</li>
                        <li>প্ল্যাটফর্মের নিরাপত্তা ও অপব্যবহার প্রতিরোধ</li>
                        <li>প্ল্যাটফর্ম উন্নয়নের জন্য অ্যানোনিমাস পরিসংখ্যান</li>
                    </ul>
                    <p class="text-sm font-semibold text-slate-700 bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 mt-4">
                        ✅ আমরা কখনো আপনার তথ্য তৃতীয় পক্ষের কাছে বিক্রি করি না।
                    </p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 4 --}}
            <section id="phone-reveal" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-base">📞</div>
                    <h2 class="text-xl font-black text-slate-900">ফোন নম্বর প্রকাশ নীতি</h2>
                </div>
                <div class="prose-custom">
                    <p>ডোনারের ফোন নম্বর ডিফল্টভাবে লুকানো থাকে। শুধুমাত্র নিচের শর্তে প্রকাশ করা হয়:</p>
                    <ul>
                        <li>রক্তের অনুরোধকারী OTP যাচাই করলে</li>
                        <li>প্রতি ২৪ ঘণ্টায় সর্বোচ্চ ৩টি নম্বর দেখা যাবে</li>
                        <li>সিস্টেমে প্রতিটি রিভিল লগ করা হয়</li>
                    </ul>
                </div>
                <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 px-5 py-4">
                    <p class="text-sm font-bold text-orange-800 mb-1">⚠️ গুরুত্বপূর্ণ</p>
                    <p class="text-xs text-orange-700 leading-relaxed">ডোনারের নম্বর ব্যবহার করে হয়রানি, স্প্যাম বা অনাকাঙ্ক্ষিত যোগাযোগ করলে অ্যাকাউন্ট স্থগিত ও আইনি ব্যবস্থা নেওয়া হবে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 5 --}}
            <section id="nid-security" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">🪪</div>
                    <h2 class="text-xl font-black text-slate-900">NID ডকুমেন্ট সুরক্ষা</h2>
                </div>
                <div class="prose-custom">
                    <p>আপলোড করা NID ডকুমেন্টের ক্ষেত্রে আমাদের নিরাপত্তা ব্যবস্থা:</p>
                    <ul>
                        <li>এনক্রিপ্টেড স্টোরেজে সংরক্ষিত, পাবলিক অ্যাক্সেস নেই</li>
                        <li>শুধুমাত্র অ্যাডমিন ভেরিফিকেশনের জন্য অ্যাক্সেস করা হয়</li>
                        <li>ভেরিফিকেশনের পর ডকুমেন্ট ডিলিট করার অনুরোধ করা যাবে</li>
                        <li>ডেটা রিটেনশন: সর্বোচ্চ ১ বছর (অ্যাকাউন্ট সক্রিয় থাকলে)</li>
                    </ul>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 6 --}}
            <section id="cookies" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-base">🍪</div>
                    <h2 class="text-xl font-black text-slate-900">কুকিজ ও ট্র্যাকিং</h2>
                </div>
                <div class="prose-custom">
                    <p>আমরা নিচের কারণে কুকিজ ব্যবহার করি:</p>
                    <ul>
                        <li><strong>সেশন কুকি:</strong> লগইন অবস্থা বজায় রাখতে (আবশ্যক)</li>
                        <li><strong>CSRF টোকেন:</strong> ফর্ম নিরাপত্তার জন্য (আবশ্যক)</li>
                        <li><strong>পছন্দ কুকি:</strong> ভাষা ও সেটিংস সংরক্ষণে (ঐচ্ছিক)</li>
                    </ul>
                    <p>আমরা কোনো তৃতীয় পক্ষের বিজ্ঞাপন ট্র্যাকিং কুকিজ ব্যবহার করি না।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 7 --}}
            <section id="user-rights" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center text-base">⚖️</div>
                    <h2 class="text-xl font-black text-slate-900">আপনার অধিকার</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                    $rights = [
                        ['icon' => '👁️', 'title' => 'তথ্য দেখার অধিকার', 'desc' => 'আপনার সম্পর্কে কী তথ্য আছে তা দেখতে পারবেন।'],
                        ['icon' => '✏️', 'title' => 'সংশোধনের অধিকার', 'desc' => 'ভুল তথ্য প্রোফাইল সেটিংস থেকে সংশোধন করুন।'],
                        ['icon' => '🗑️', 'title' => 'মুছে ফেলার অধিকার', 'desc' => 'অ্যাকাউন্ট ডিলিট করলে সব ব্যক্তিগত তথ্য সরানো হবে।'],
                        ['icon' => '🚫', 'title' => 'প্রক্রিয়ার বিরোধিতা', 'desc' => 'মার্কেটিং বা অপ্রয়োজনীয় প্রক্রিয়া বন্ধ করতে পারবেন।'],
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

            {{-- Section 8 --}}
            <section id="contact-us" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-base">✉️</div>
                    <h2 class="text-xl font-black text-slate-900">যোগাযোগ করুন</h2>
                </div>
                <div class="prose-custom">
                    <p>প্রাইভেসি সংক্রান্ত যেকোনো অভিযোগ বা অনুরোধের জন্য:</p>
                </div>
                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 flex flex-col sm:flex-row gap-4 items-start">
                    <div class="flex-1">
                        <p class="text-sm font-bold text-slate-800 mb-1">রক্তদূত প্রাইভেসি টিম</p>
                        <p class="text-sm text-slate-600">ইমেইল: <a href="mailto:privacy@roktodut.com" class="text-red-600 font-semibold hover:underline">privacy@roktodut.com</a></p>
                        <p class="text-sm text-slate-600 mt-0.5">সাড়া দেওয়ার সময়: ৭২ ঘণ্টার মধ্যে</p>
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
