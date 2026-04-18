@extends('layouts.app')

@section('title', 'পয়েন্ট ও ব্যাজ গাইড – রক্তদূত')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 sm:py-12">

    {{-- ══════════ Hero ══════════ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 p-8 sm:p-12 mb-10 shadow-2xl">
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/5 rounded-full -translate-y-1/3 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4"></div>

        <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-2xl shadow-lg">🪙</div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">পয়েন্ট ও ব্যাজ গাইড</h1>
                        <p class="text-orange-100 text-sm font-semibold">রক্তদূত গ্যামিফিকেশন সিস্টেম</p>
                    </div>
                </div>
                <p class="text-orange-100 text-sm max-w-md leading-relaxed">
                    রক্তদান করুন, পয়েন্ট অর্জন করুন এবং বিশেষ ব্যাজ আনলক করুন। আপনার অবদানের যথাযথ স্বীকৃতি পান।
                </p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('leaderboard') }}"
                    class="inline-flex items-center gap-2 bg-white/20 backdrop-blur border border-white/30 text-white font-black text-sm px-5 py-3 rounded-2xl hover:bg-white/30 transition-all">
                    🏆 লিডারবোর্ড দেখুন
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════ পয়েন্ট আর্নিং সিস্টেম ══════════ --}}
    <section class="mb-10">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-lg">🪙</div>
            <h2 class="text-xl font-black text-slate-800">পয়েন্ট আর্নিং সিস্টেম</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Primary Action --}}
            <div class="sm:col-span-2 relative overflow-hidden rounded-2xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-rose-50 p-5">
                <div class="absolute top-3 right-4 text-5xl opacity-10">🩸</div>
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-red-600 flex items-center justify-center text-2xl shadow-md flex-shrink-0">🩸</div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h3 class="font-black text-slate-800 text-base">সফল রক্তদান</h3>
                            <span class="text-2xl font-black text-red-600">+৫০ পয়েন্ট</span>
                        </div>
                        <p class="text-slate-600 text-sm mt-1 font-medium">প্রতিটি সফল ও ভেরিফাইড রক্তদানের পর এই পয়েন্ট পাবেন। এটি সর্বোচ্চ পয়েন্টের কাজ।</p>
                        <span class="inline-flex items-center mt-2 text-[11px] font-black text-red-700 bg-red-100 border border-red-200 rounded-full px-2.5 py-0.5">⭐ সর্বোচ্চ মূল্যায়ন</span>
                    </div>
                </div>
            </div>

            @php
            $earning = [
                [
                    'emoji' => '⚡', 'bg' => 'bg-orange-500', 'border' => 'border-orange-200', 'card' => 'from-orange-50 to-amber-50',
                    'title' => 'First Responder বোনাস', 'points' => '+১০', 'color' => 'text-orange-600',
                    'desc'  => 'ইমার্জেন্সি রিকোয়েস্টে ৩ ঘণ্টার মধ্যে রেসপন্ড করে রক্ত দিলে সাধারণ ৫০ পয়েন্টের সাথে বোনাস।',
                    'tag'   => '⏱ দ্রুত রেসপন্সে', 'tag_color' => 'text-orange-700 bg-orange-100 border-orange-200',
                ],
                [
                    'emoji' => '🎓', 'bg' => 'bg-blue-500', 'border' => 'border-blue-200', 'card' => 'from-blue-50 to-indigo-50',
                    'title' => 'রেফারেল সাইন-আপ', 'points' => '+১০', 'color' => 'text-blue-600',
                    'desc'  => 'আপনার ইউনিক রেফারেল কোড ব্যবহার করে কেউ রেজিস্ট্রেশন করলে এবং প্রোফাইল ভেরিফাই করলে পাবেন।',
                    'tag'   => '👥 রেফারেলে', 'tag_color' => 'text-blue-700 bg-blue-100 border-blue-200',
                ],
                [
                    'emoji' => '🎁', 'bg' => 'bg-emerald-500', 'border' => 'border-emerald-200', 'card' => 'from-emerald-50 to-teal-50',
                    'title' => 'রেফারড এর প্রথম ডোনেশন', 'points' => '+৩০', 'color' => 'text-emerald-600',
                    'desc'  => 'আপনার রেফার করা ব্যক্তি জীবনে প্রথমবার রক্তদান করলে এই বিশেষ বোনাস পয়েন্ট পাবেন।',
                    'tag'   => '🎯 বিশেষ বোনাস', 'tag_color' => 'text-emerald-700 bg-emerald-100 border-emerald-200',
                ],
                [
                    'emoji' => '💬', 'bg' => 'bg-purple-500', 'border' => 'border-purple-200', 'card' => 'from-purple-50 to-fuchsia-50',
                    'title' => 'গ্রহীতার পজিটিভ রিভিউ', 'points' => '+১০', 'color' => 'text-purple-600',
                    'desc'  => 'রক্ত পাওয়ার পর গ্রহীতা (Recipient) যদি আপনার ডোনেশন কনফার্ম করেন, তবে এই পয়েন্ট যোগ হবে।',
                    'tag'   => '✅ রিভিউতে', 'tag_color' => 'text-purple-700 bg-purple-100 border-purple-200',
                ],
                [
                    'emoji' => '✅', 'bg' => 'bg-teal-500', 'border' => 'border-teal-200', 'card' => 'from-teal-50 to-cyan-50',
                    'title' => 'প্রোফাইল ১০০% কমপ্লিট', 'points' => '+২০', 'color' => 'text-teal-600',
                    'desc'  => 'প্রথমবারের মতো প্রোফাইল ১০০% কমপ্লিট এবং NID ভেরিফাই করলে এই বোনাস পাওয়া যাবে।',
                    'tag'   => '🔒 একবারের জন্য', 'tag_color' => 'text-teal-700 bg-teal-100 border-teal-200',
                ],
            ];
            @endphp

            @foreach($earning as $item)
            <div class="relative overflow-hidden rounded-2xl border {{ $item['border'] }} bg-gradient-to-r {{ $item['card'] }} p-5">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl {{ $item['bg'] }} flex items-center justify-center text-xl shadow-sm flex-shrink-0">{{ $item['emoji'] }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <h3 class="font-black text-slate-800 text-sm">{{ $item['title'] }}</h3>
                            <span class="font-black {{ $item['color'] }} text-base">{{ $item['points'] }}</span>
                        </div>
                        <p class="text-slate-600 text-xs mt-1 leading-relaxed">{{ $item['desc'] }}</p>
                        <span class="inline-flex items-center mt-2 text-[10px] font-black {{ $item['tag_color'] }} border rounded-full px-2 py-0.5">{{ $item['tag'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ মাইলস্টোন ব্যাজ ══════════ --}}
    <section class="mb-10">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-lg bg-yellow-100 flex items-center justify-center text-lg">🏅</div>
            <h2 class="text-xl font-black text-slate-800">মাইলস্টোন ব্যাজ</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
            $milestones = [
                [
                    'emoji' => '🥉', 'name' => 'Bronze Bloodline', 'bn' => 'ব্রোঞ্জ ব্লাডলাইন',
                    'bg' => 'from-amber-50 to-yellow-50', 'border' => 'border-amber-200', 'name_color' => 'text-amber-800',
                    'badge_bg' => 'bg-amber-100', 'cond_donation' => '১ বার', 'cond_points' => '৫০ pts',
                    'desc' => 'রক্তদাতা হিসেবে আপনার যাত্রা শুরু হলো। প্রথম অবদানের স্বীকৃতি।',
                ],
                [
                    'emoji' => '🥈', 'name' => 'Silver Savior', 'bn' => 'সিলভার সেভিয়ার',
                    'bg' => 'from-slate-50 to-gray-50', 'border' => 'border-slate-200', 'name_color' => 'text-slate-700',
                    'badge_bg' => 'bg-slate-100', 'cond_donation' => '৫ বার', 'cond_points' => '৩০০ pts',
                    'desc' => 'আপনি একজন নিয়মিত রক্তদাতা। আপনার প্রতিশ্রুতি সত্যিই অনুপ্রেরণাদায়ক।',
                ],
                [
                    'emoji' => '🏅', 'name' => 'Golden Guardian', 'bn' => 'গোল্ডেন গার্ডিয়ান',
                    'bg' => 'from-yellow-50 to-amber-50', 'border' => 'border-yellow-200', 'name_color' => 'text-yellow-800',
                    'badge_bg' => 'bg-yellow-100', 'cond_donation' => '১০ বার', 'cond_points' => '৬০০ pts',
                    'desc' => 'আপনি এক অসাধারণ জীবনরক্ষী। এই পর্যায়ে আনুমানিক ১০+ জীবন বেঁচেছে।',
                ],
                [
                    'emoji' => '🏆', 'name' => 'Platinum Hero', 'bn' => 'প্লাটিনাম হিরো',
                    'bg' => 'from-purple-50 to-fuchsia-50', 'border' => 'border-purple-200', 'name_color' => 'text-purple-800',
                    'badge_bg' => 'bg-purple-100', 'cond_donation' => '২০+ বার', 'cond_points' => '১৫০০+ pts',
                    'desc' => 'সর্বোচ্চ সম্মান। আপনি রক্তদূত কমিউনিটির সত্যিকারের নায়ক।',
                    'highlight' => true,
                ],
            ];
            @endphp

            @foreach($milestones as $m)
            <div class="relative overflow-hidden rounded-2xl border {{ $m['border'] }} bg-gradient-to-br {{ $m['bg'] }} p-5 h-full">
                @if(isset($m['highlight']))
                    <div class="absolute top-3 right-4 text-6xl opacity-[0.07]">🏆</div>
                @endif
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl {{ $m['badge_bg'] }} flex items-center justify-center text-4xl shadow-sm flex-shrink-0">{{ $m['emoji'] }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-black text-slate-900 text-base">{{ $m['name'] }}</h3>
                            <span class="text-xs font-bold {{ $m['name_color'] }} opacity-70">{{ $m['bn'] }}</span>
                        </div>
                        <p class="text-slate-600 text-xs mt-1 leading-relaxed">{{ $m['desc'] }}</p>
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-slate-600 bg-white border border-slate-200 rounded-full px-2.5 py-0.5">
                                🩸 {{ $m['cond_donation'] }} রক্তদান
                            </span>
                            <span class="text-slate-400 text-xs font-bold">অথবা</span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-slate-600 bg-white border border-slate-200 rounded-full px-2.5 py-0.5">
                                🪙 {{ $m['cond_points'] }} অর্জন
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ স্পেশাল ব্যাজ ══════════ --}}
    <section class="mb-10">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-lg">✨</div>
            <h2 class="text-xl font-black text-slate-800">স্পেশাল আইডেন্টিটি ব্যাজ</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
            $special = [
                [
                    'emoji' => '🎓', 'name' => 'Campus Hero', 'bn' => 'ক্যাম্পাস হিরো',
                    'bg' => 'from-blue-50 to-indigo-50', 'border' => 'border-blue-200', 'badge_bg' => 'bg-blue-100',
                    'how' => '.edu বা .ac.bd ইমেইল দিয়ে রেজিস্টার করলে অটোমেটিক পাবেন।',
                    'tag' => 'অটোমেটিক', 'tag_color' => 'text-blue-700 bg-blue-100 border-blue-200',
                ],
                [
                    'emoji' => '🛡️', 'name' => 'Verified Donor', 'bn' => 'ভেরিফাইড ডোনার',
                    'bg' => 'from-emerald-50 to-teal-50', 'border' => 'border-emerald-200', 'badge_bg' => 'bg-emerald-100',
                    'how' => 'NID বা জাতীয় পরিচয়পত্র আপলোড করে অ্যাডমিন ভেরিফিকেশন পাস করলে।',
                    'tag' => 'ম্যানুয়াল ভেরিফিকেশন', 'tag_color' => 'text-emerald-700 bg-emerald-100 border-emerald-200',
                ],
                [
                    'emoji' => '⚡', 'name' => 'Ready Now', 'bn' => 'রেডি নাউ',
                    'bg' => 'from-orange-50 to-amber-50', 'border' => 'border-orange-200', 'badge_bg' => 'bg-orange-100',
                    'how' => 'প্রোফাইলে "ইমার্জেন্সি মোড" চালু রাখলে এই ব্যাজটি দৃশ্যমান হবে।',
                    'tag' => 'সেটিং থেকে চালু করুন', 'tag_color' => 'text-orange-700 bg-orange-100 border-orange-200',
                ],
                [
                    'emoji' => '💎', 'name' => 'Rare Blood Hero', 'bn' => 'রেয়ার ব্লাড হিরো',
                    'bg' => 'from-pink-50 to-rose-50', 'border' => 'border-pink-200', 'badge_bg' => 'bg-pink-100',
                    'how' => 'নেগেটিভ গ্রুপ (O-, A-, B-, AB-) এবং কমপক্ষে একবার রক্তদান করলে।',
                    'tag' => 'বিরল রক্তে', 'tag_color' => 'text-pink-700 bg-pink-100 border-pink-200',
                ],
                [
                    'emoji' => '🌙', 'name' => 'Midnight Savior', 'bn' => 'মিডনাইট সেভিয়ার',
                    'bg' => 'from-indigo-50 to-purple-50', 'border' => 'border-indigo-200', 'badge_bg' => 'bg-indigo-100',
                    'how' => 'রাত ১২টা থেকে ভোর ৬টার মধ্যে ইমার্জেন্সিতে রক্তদান করলে এই বিরল ব্যাজ পাবেন।',
                    'tag' => 'রাতের বীর', 'tag_color' => 'text-indigo-700 bg-indigo-100 border-indigo-200',
                ],
            ];
            @endphp

            @foreach($special as $s)
            <div class="relative rounded-2xl border {{ $s['border'] }} bg-gradient-to-r {{ $s['bg'] }} p-5">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl {{ $s['badge_bg'] }} flex items-center justify-center text-2xl shadow-sm flex-shrink-0">{{ $s['emoji'] }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-black text-slate-900 text-sm">{{ $s['name'] }}</h3>
                            <span class="text-[11px] text-slate-500 font-semibold">{{ $s['bn'] }}</span>
                        </div>
                        <p class="text-slate-600 text-xs mt-1 leading-relaxed">{{ $s['how'] }}</p>
                        <span class="inline-flex items-center mt-2 text-[10px] font-black {{ $s['tag_color'] }} border rounded-full px-2 py-0.5">{{ $s['tag'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ CTA ══════════ --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('leaderboard') }}"
            class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-black px-6 py-3 rounded-2xl shadow-sm transition-all">
            🏆 লিডারবোর্ড দেখুন
        </a>
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-700 font-black px-6 py-3 rounded-2xl border border-slate-200 shadow-sm transition-all">
            📊 আমার ড্যাশবোর্ড
        </a>
    </div>

</div>
@endsection
