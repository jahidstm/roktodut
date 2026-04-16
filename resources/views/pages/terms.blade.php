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
        <p class="mt-2 text-slate-400 text-sm font-medium">সর্বশেষ আপডেট: ১৬ এপ্রিল, ২০২৬ · রক্তদূত প্ল্যাটফর্ম</p>
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
                    রক্তদূত প্ল্যাটফর্ম ব্যবহার করার আগে এই শর্তাবলী মনোযোগ দিয়ে পড়ুন। প্ল্যাটফর্ম ব্যবহার করলে আপনি এই শর্তগুলোতে সম্মতি দিচ্ছেন বলে গণ্য হবে।
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
            {{-- Section 1 --}}
            <section id="acceptance" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">✅</div>
                    <h2 class="text-xl font-black text-slate-900">শর্ত গ্রহণ</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদূত-এ <strong>অ্যাকাউন্ট তৈরি করে, লগইন করে</strong> বা যেকোনো সেবা ব্যবহার করে আপনি নিচের শর্তগুলো মেনে নিচ্ছেন। আপনি যদি শর্তাবলীতে সম্মত না হন, তবে প্ল্যাটফর্ম ব্যবহার বন্ধ করুন।</p>
                    <p>আমরা যেকোনো সময় এই শর্ত আপডেট করতে পারি। পরিবর্তন হলে ইমেইল বা নোটিফিকেশনের মাধ্যমে জানানো হবে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 2 --}}
            <section id="eligibility" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-base">🎂</div>
                    <h2 class="text-xl font-black text-slate-900">যোগ্যতার শর্ত</h2>
                </div>
                <div class="prose-terms">
                    <p>প্ল্যাটফর্ম ব্যবহার করতে হলে আপনাকে:</p>
                    <ul>
                        <li>কমপক্ষে ১৮ বছর বয়সী হতে হবে</li>
                        <li>বাংলাদেশের নাগরিক বা বৈধ বাসিন্দা হতে হবে</li>
                        <li>একটি বৈধ ইমেইল ঠিকানা দিয়ে রেজিস্ট্রেশন করতে হবে</li>
                        <li>প্রদত্ত তথ্য সত্য ও সঠিক হতে হবে</li>
                    </ul>
                    <p>ভুয়া তথ্য দিলে বা শর্ত লঙ্ঘন করলে অ্যাকাউন্ট স্থগিত করা হবে।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 3 --}}
            <section id="account" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-base">🔐</div>
                    <h2 class="text-xl font-black text-slate-900">অ্যাকাউন্ট দায়িত্ব</h2>
                </div>
                <div class="prose-terms">
                    <p>আপনার অ্যাকাউন্ট সম্পূর্ণ আপনার দায়িত্বে। এর মধ্যে অন্তর্ভুক্ত:</p>
                    <ul>
                        <li>পাসওয়ার্ড গোপন রাখা ও নিরাপদ রক্ষা করা</li>
                        <li>আপনার নামে হওয়া সব কার্যক্রমের দায়িত্ব</li>
                        <li>অ্যাকাউন্ট হ্যাক বা অননুমোদিত প্রবেশের তাৎক্ষণিক রিপোর্ট</li>
                        <li>একটি ইমেইলে একটিমাত্র অ্যাকাউন্ট রাখা</li>
                    </ul>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 4 --}}
            <section id="donor-rules" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-base">🩸</div>
                    <h2 class="text-xl font-black text-slate-900">ডোনার হিসেবে বিধিমালা</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদাতা হিসেবে আপনাকে নিশ্চিত করতে হবে যে:</p>
                    <ul>
                        <li>আপনার স্বাস্থ্য রক্তদানের জন্য উপযুক্ত</li>
                        <li>সর্বশেষ রক্তদানের পর কমপক্ষে ১২০ দিন অতিবাহিত হয়েছে</li>
                        <li>আপনি কোনো সংক্রামক রোগে আক্রান্ত নন</li>
                        <li>রক্তদানের আগে যথেষ্ট খাবার ও পানি গ্রহণ করেছেন</li>
                    </ul>
                    <p>মিথ্যা স্বাস্থ্য তথ্য দিয়ে রক্তদান করা আইনত দণ্ডনীয়।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 5 --}}
            <section id="prohibited" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-base">🚫</div>
                    <h2 class="text-xl font-black text-slate-900">নিষিদ্ধ কার্যক্রম</h2>
                </div>
                <div class="prose-terms">
                    <p>নিচের কার্যক্রম কঠোরভাবে নিষিদ্ধ:</p>
                </div>
                <div class="mt-4 space-y-2">
                    @php
                    $prohibited = [
                        ['icon' => '💰', 'text' => 'রক্তের বিনিময়ে অর্থ গ্রহণ বা দাবি করা'],
                        ['icon' => '📞', 'text' => 'ডোনারের নম্বর ব্যবহার করে হয়রানি বা স্প্যাম করা'],
                        ['icon' => '🤖', 'text' => 'বট, স্ক্রিপ্ট বা অটোমেশন দিয়ে প্ল্যাটফর্ম ব্যবহার'],
                        ['icon' => '🎭', 'text' => 'ভুয়া পরিচয় বা অন্যের পরিচয় ধারণ করা'],
                        ['icon' => '🛡️', 'text' => 'ভুয়া রক্তের অনুরোধ তৈরি করা'],
                        ['icon' => '🔓', 'text' => 'সিস্টেমের নিরাপত্তা ভাঙার চেষ্টা'],
                    ];
                    @endphp
                    @foreach($prohibited as $p)
                    <div class="flex items-center gap-3 rounded-xl border border-rose-100 bg-rose-50/60 px-4 py-3">
                        <span class="text-base shrink-0">{{ $p['icon'] }}</span>
                        <p class="text-sm text-slate-700 font-medium">{{ $p['text'] }}</p>
                    </div>
                    @endforeach
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 6 --}}
            <section id="liability" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-base">⚖️</div>
                    <h2 class="text-xl font-black text-slate-900">দায়বদ্ধতার সীমা</h2>
                </div>
                <div class="prose-terms">
                    <p>রক্তদূত একটি মধ্যস্থতাকারী প্ল্যাটফর্ম। আমরা সরাসরি চিকিৎসা সেবা প্রদান করি না। তাই:</p>
                    <ul>
                        <li>রক্তের মান বা প্রাপ্যতার নিশ্চয়তা আমরা দিতে পারি না</li>
                        <li>ডোনার-রোগীর মধ্যে ব্যক্তিগত বিরোধের দায় আমাদের নয়</li>
                        <li>তৃতীয় পক্ষের কার্যক্রমের দায় আমরা বহন করব না</li>
                        <li>প্ল্যাটফর্ম ডাউনটাইম বা প্রযুক্তিগত সমস্যায় ক্ষতির দায় সীমিত</li>
                    </ul>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-5 py-4">
                    <p class="text-xs text-slate-600 leading-relaxed font-medium">
                        <strong>জরুরি নোট:</strong> জরুরি রক্তের জন্য সরকারি হাসপাতালের ব্লাড ব্যাংক এবং জাতীয় হেল্পলাইন ব্যবহার করুন। রক্তদূত একটি সম্পূরক সেবা।
                    </p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 7 --}}
            <section id="termination" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-base">🔚</div>
                    <h2 class="text-xl font-black text-slate-900">অ্যাকাউন্ট বন্ধ ও স্থগিত</h2>
                </div>
                <div class="prose-terms">
                    <p>নিচের ক্ষেত্রে আমরা বিনা নোটিশে অ্যাকাউন্ট বন্ধ করতে পারি:</p>
                    <ul>
                        <li>শর্তাবলী লঙ্ঘনের যেকোনো ঘটনায়</li>
                        <li>অন্য ব্যবহারকারীর রিপোর্ট যাচাইয়ের পর</li>
                        <li>দীর্ঘদিন (১ বছর+) নিষ্ক্রিয় থাকলে</li>
                    </ul>
                    <p>আপনি নিজেও যেকোনো সময় প্রোফাইল সেটিংস থেকে অ্যাকাউন্ট ডিলিট করতে পারবেন।</p>
                </div>
            </section>

            <hr class="border-slate-100 mb-10">

            {{-- Section 8 --}}
            <section id="governing-law" class="mb-10 scroll-mt-20">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-base">🏛️</div>
                    <h2 class="text-xl font-black text-slate-900">প্রযোজ্য আইন</h2>
                </div>
                <div class="prose-terms">
                    <p>এই শর্তাবলী বাংলাদেশের আইন দ্বারা পরিচালিত। যেকোনো বিরোধ নিষ্পত্তি ঢাকার আদালতে হবে।</p>
                    <p>প্রযোজ্য আইন: ডিজিটাল নিরাপত্তা আইন ২০১৮, তথ্য ও যোগাযোগ প্রযুক্তি আইন ২০০৬ (সংশোধিত)।</p>
                </div>
            </section>

            {{-- Contact CTA --}}
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-800">শর্তাবলী নিয়ে প্রশ্ন আছে?</p>
                    <p class="text-xs text-slate-500 mt-0.5">আমাদের দল ৪৮ ঘণ্টার মধ্যে উত্তর দেবে।</p>
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
</style>
@endpush

@endsection
