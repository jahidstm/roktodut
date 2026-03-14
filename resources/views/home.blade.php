<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>রক্তদূত — জরুরি রক্ত সহায়তা</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-x-hidden">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                    <span class="text-red-600 font-extrabold tracking-tight">RD</span>
                </div>
                <div class="leading-tight">
                    <div class="text-lg font-extrabold tracking-tight">রক্তদূত</div>
                    <div class="text-xs text-slate-500 font-semibold">ইমার্জেন্সি ব্লাড নেটওয়ার্ক</div>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-7 font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="text-red-600">হোম</a>
                <a href="#donate" class="hover:text-red-600 transition">রক্ত দিন</a>
                <a href="#urgent" class="hover:text-red-600 transition">জরুরি অনুরোধ</a>
                <a href="{{ route('login') }}" class="hover:text-red-600 transition">ডোনার ফিড</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold text-slate-700 hover:text-red-600 transition">
                    লগইন
                </a>

                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center bg-red-600 text-white px-5 py-2.5 rounded-lg font-extrabold hover:bg-red-700 transition shadow-sm shadow-red-200">
                    রেজিস্টার
                </a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-red-50 via-white to-white"></div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 pt-16 pb-28 lg:pt-24 lg:pb-36">
            <div class="flex flex-col lg:flex-row items-center gap-14">
                <!-- Text -->
                <div class="lg:w-1/2 text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-red-100">
                        ইমার্জেন্সি ব্লাড ডোনেশন নেটওয়ার্ক
                    </span>

                    <h1 class="mt-6 text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.12] tracking-tight">
                        জরুরি মুহূর্তে রক্তের সন্ধানে—
                        <span class="text-red-600">আমরা আছি আপনার পাশে</span>
                    </h1>

                    <p class="mt-6 text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-medium">
                        রক্তদূত প্ল্যাটফর্মের মাধ্যমে আপনার এলাকার ভেরিফায়েড ডোনারদের সাথে দ্রুত সংযোগ করুন।
                        রক্ত দিন, জীবন বাঁচান।
                    </p>

                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center bg-red-600 text-white px-7 py-3.5 rounded-lg font-extrabold shadow-sm shadow-red-200 hover:bg-red-700 transition">
                            রক্তের রিকোয়েস্ট করুন
                        </a>

                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center border-2 border-red-600 text-red-600 px-7 py-3.5 rounded-lg font-extrabold hover:bg-red-50 transition">
                            ডোনার ফিড দেখুন
                        </a>
                    </div>

                    <div class="mt-10 flex flex-wrap justify-center lg:justify-start gap-3 text-xs font-semibold text-slate-600">
                        <span class="px-3 py-1.5 rounded-full bg-white border border-slate-200">OTP রিভিল</span>
                        <span class="px-3 py-1.5 rounded-full bg-white border border-slate-200">ভেরিফায়েড ইউজার</span>
                        <span class="px-3 py-1.5 rounded-full bg-white border border-slate-200">জেলা-ভিত্তিক ম্যাচ</span>
                    </div>
                </div>

                <!-- Illustration -->
                <div class="lg:w-1/2 flex justify-center relative">
                    <div class="relative w-72 h-72 md:w-96 md:h-96 bg-red-50 rounded-full flex items-center justify-center">
                        <div class="absolute inset-0 border-[18px] border-white rounded-full shadow-2xl z-10"></div>
                        <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-20"></div>

                        <svg class="w-24 md:w-36 text-red-500 z-20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>

                        <div class="absolute -top-4 right-10 bg-white px-3 py-2 rounded-full shadow-md font-extrabold text-red-600 z-30 text-sm">O+</div>
                        <div class="absolute top-1/2 -left-6 bg-white px-3 py-2 rounded-full shadow-md font-extrabold text-red-600 z-30 text-sm">AB+</div>
                        <div class="absolute bottom-4 left-10 bg-white px-3 py-2 rounded-full shadow-md font-extrabold text-red-600 z-30 text-sm">A-</div>
                        <div class="absolute bottom-10 right-0 bg-white px-3 py-2 rounded-full shadow-md font-extrabold text-red-600 z-30 text-sm">B+</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick search -->
    <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 relative -mt-16 md:-mt-20">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 class="text-lg font-extrabold text-slate-800">দ্রুত অনুসন্ধান করুন</h3>
                <span class="text-xs font-semibold text-slate-500">(লগইন + ভেরিফিকেশন প্রয়োজন)</span>
            </div>

            <form action="{{ route('login') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <select class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200">
                    <option>বিভাগ নির্বাচন</option>
                    <option>ঢাকা</option><option>চট্টগ্রাম</option><option>রাজশাহী</option><option>খুলনা</option>
                    <option>সিলেট</option><option>বরিশাল</option><option>রংপুর</option><option>ময়মনসিংহ</option>
                </select>
                <select class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200">
                    <option>জেলা নির্বাচন</option>
                    <option>ঢাকা</option><option>চট্টগ্রাম</option><option>রাজশাহী</option>
                </select>
                <select class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200">
                    <option>উপজেলা/এরিয়া</option>
                    <option>ধানমন্ডি</option><option>উত্তরা</option><option>মিরপুর</option>
                </select>
                <select class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200">
                    <option>রক্তের গ্রুপ</option>
                    <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
                    <option>AB+</option><option>AB-</option><option>O+</option><option>O-</option>
                </select>

                <button type="submit"
                        class="bg-red-600 text-white font-extrabold rounded-lg py-3.5 hover:bg-red-700 transition shadow-sm shadow-red-200">
                    লগইন করে খুঁজুন
                </button>
            </form>
        </div>
    </section>

    <!-- Stats -->
    <section id="donate" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 mt-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">৫০০+</div>
                <div class="text-slate-500 mt-2 font-semibold">ভেরিফাইড ডোনার</div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">১২০+</div>
                <div class="text-slate-500 mt-2 font-semibold">সফল কানেকশন</div>
            </div>
            <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm">
                <div class="text-4xl font-extrabold text-red-600">৬৪</div>
                <div class="text-slate-500 mt-2 font-semibold">জেলায় সেবা চালু</div>
            </div>
        </div>
    </section>

    <!-- Urgent -->
    <section id="urgent" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 py-20">
        <div class="text-center mb-12">
            <span class="text-red-500 font-extrabold text-sm tracking-widest uppercase">জরুরি</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">
                জরুরি <span class="text-red-600">রক্তের প্রয়োজন</span>
            </h2>
            <p class="text-slate-500 mt-3 font-medium">এই রোগীদের এখনই আপনার সাহায্য প্রয়োজন</p>
        </div>

        @php
            $cards = [
                ['name'=>'রহিম উদ্দিন','place'=>'ঢাকা মেডিকেল কলেজ হাসপাতাল','area'=>'সাভার, ঢাকা','bg'=>'O+','time'=>'২ ঘণ্টা আগে পোস্ট করা হয়েছে'],
                ['name'=>'ফাতেমা বেগম','place'=>'ইবনে সিনা হাসপাতাল','area'=>'গুলশান, ঢাকা','bg'=>'B-','time'=>'৪ ঘণ্টা আগে পোস্ট করা হয়েছে'],
                ['name'=>'করিম মিয়া','place'=>'চট্টগ্রাম মেডিকেল কলেজ','area'=>'হালিশহর, চট্টগ্রাম','bg'=>'AB+','time'=>'৫ ঘণ্টা আগে পোস্ট করা হয়েছে'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($cards as $c)
                <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4 gap-3">
                            <div>
                                <h3 class="font-extrabold text-xl text-slate-900">{{ $c['name'] }}</h3>
                                <p class="text-slate-500 text-sm mt-1 font-medium">{{ $c['place'] }}</p>
                            </div>
                            <span class="bg-red-50 text-red-600 font-extrabold px-3 py-1.5 rounded-lg border border-red-100 shadow-sm">
                                {{ $c['bg'] }}
                            </span>
                        </div>

                        <div class="bg-slate-50 rounded-lg p-4 space-y-3 mt-4 border border-slate-100">
                            <div class="flex items-center gap-3 text-slate-600 text-sm font-semibold">
                                <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>
                                {{ $c['area'] }}
                            </div>
                            <div class="flex items-center gap-3 text-slate-500 text-sm font-semibold">
                                <span class="inline-block w-2 h-2 rounded-full bg-slate-300"></span>
                                {{ $c['time'] }}
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('login') }}"
                       class="mt-6 w-full bg-red-600 text-white font-extrabold py-3.5 rounded-lg hover:bg-red-700 transition shadow-sm shadow-red-200 flex justify-center items-center gap-2">
                        লগইন করে কল করুন
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center border border-slate-300 bg-white text-slate-700 font-extrabold px-7 py-3 rounded-lg hover:bg-slate-50 transition shadow-sm">
                সব দেখুন
            </a>
        </div>
    </section>

    <footer class="bg-white border-t border-slate-100 py-8 text-center text-slate-500 font-medium">
        <p>© {{ date('Y') }} রক্তদূত. সর্বস্বত্ব সংরক্ষিত.</p>
    </footer>
</body>
</html>