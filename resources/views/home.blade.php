<!doctype html>
<html lang="bn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>রক্তদূত — জরুরি রক্ত সহায়তা</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root { font-family: 'Hind Siliguri', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased overflow-x-hidden">
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center">
                    <span class="text-red-600 font-extrabold tracking-tight">RD</span>
                </div>
                <div class="leading-tight">
                    <div class="text-lg font-extrabold tracking-tight">রক্তদূত</div>
                    <div class="text-xs text-slate-500 font-semibold">ইমার্জেন্সি ব্লাড নেটওয়ার্ক</div>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-7 font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="text-red-600">হোম</a>
                <a href="#donate" class="hover:text-red-600 transition">রক্ত দিন</a>
                <a href="#urgent" class="hover:text-red-600 transition">জরুরি অনুরোধ</a>
                <a href="{{ route('requests.index') }}" class="hover:text-red-600 transition">ডোনার ফিড</a>
            </nav>

            {{-- 🎯 Dynamic Auth Buttons --}}
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold text-slate-700 hover:text-red-600 transition">লগইন</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-red-600 text-white px-5 py-2.5 rounded-lg font-extrabold hover:bg-red-700 transition shadow-sm shadow-red-200">রেজিস্টার</a>
                @endguest

                @auth
                    @php
                        $dashboardRoute = auth()->user()->role === 'org_admin' ? route('org.dashboard') : route('dashboard');
                    @endphp
                    <a href="{{ route('profile.edit') }}" class="hidden sm:inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold text-slate-700 hover:text-red-600 transition">প্রোফাইল</a>
                    <a href="{{ $dashboardRoute }}" class="inline-flex items-center justify-center bg-slate-900 text-white px-5 py-2.5 rounded-lg font-extrabold hover:bg-slate-800 transition shadow-sm gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        ড্যাশবোর্ড
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <section class="relative bg-white overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-red-50 via-white to-white"></div>
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-10 pt-16 pb-28 lg:pt-24 lg:pb-36">
            <div class="flex flex-col lg:flex-row items-center gap-14">
                <div class="lg:w-1/2 text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-4 py-1.5 rounded-full text-sm font-extrabold tracking-wide border border-red-100">ইমার্জেন্সি ব্লাড ডোনেশন নেটওয়ার্ক</span>
                    <h1 class="mt-6 text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.12] tracking-tight">
                        জরুরি মুহূর্তে রক্তের সন্ধানে—<span class="text-red-600">আমরা আছি আপনার পাশে</span>
                    </h1>
                    <p class="mt-6 text-lg text-slate-600 leading-relaxed max-w-2xl mx-auto lg:mx-0 font-medium">
                        রক্তদূত প্ল্যাটফর্মের মাধ্যমে আপনার এলাকার ভেরিফায়েড ডোনারদের সাথে দ্রুত সংযোগ করুন। রক্ত দিন, জীবন বাঁচান।
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('requests.create') }}" class="inline-flex items-center justify-center bg-red-600 text-white px-7 py-3.5 rounded-lg font-extrabold shadow-sm shadow-red-200 hover:bg-red-700 transition">রক্তের রিকোয়েস্ট করুন</a>
                        <a href="{{ route('search') }}" class="inline-flex items-center justify-center border-2 border-red-600 text-red-600 px-7 py-3.5 rounded-lg font-extrabold hover:bg-red-50 transition">ডোনার খুঁজুন</a>
                    </div>
                </div>

                <div class="lg:w-1/2 flex justify-center relative">
                    <div class="relative w-72 h-72 md:w-96 md:h-96 bg-red-50 rounded-full flex items-center justify-center">
                        <div class="absolute inset-0 border-[18px] border-white rounded-full shadow-2xl z-10"></div>
                        <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-20"></div>
                        <svg class="w-24 md:w-36 text-red-500 z-20" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 🔍 DYNAMIC AJAX SEARCH SECTION --}}
    <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 relative -mt-16 md:-mt-20">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 class="text-lg font-extrabold text-slate-800">দ্রুত অনুসন্ধান করুন</h3>
            </div>

            <form action="{{ route('search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
                {{-- 🎯 অপটিমাইজড বিভাগ ড্রপডাউন (ডাটাবেস থেকে ডেটা লোড হচ্ছে) --}}
                <select id="division_select" name="division" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200">
                    <option value="">বিভাগ নির্বাচন</option>
                    @if(isset($divisions))
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    @endif
                </select>

                <select id="district_select" name="district" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200" disabled>
                    <option value="">জেলা নির্বাচন</option>
                </select>

                <select id="upazila_select" name="upazila" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200" disabled>
                    <option value="">উপজেলা/এরিয়া</option>
                </select>

                <select name="blood_group" class="p-3.5 border border-slate-200 rounded-lg bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:border-red-500 focus:ring-red-200" required>
                    <option value="">রক্তের গ্রুপ</option>
                    <option value="A+">A+</option><option value="A-">A-</option><option value="B+">B+</option><option value="B-">B-</option>
                    <option value="AB+">AB+</option><option value="AB-">AB-</option><option value="O+">O+</option><option value="O-">O-</option>
                </select>

                <button type="submit" class="bg-red-600 text-white font-extrabold rounded-lg py-3.5 hover:bg-red-700 transition shadow-sm shadow-red-200">
                    খুঁজুন
                </button>
            </form>
        </div>
    </section>

    {{-- Stats Section --}}
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
                <div class="text-slate-500 mt-2 font-semibold">জেলায় সেবা চালু</div>
            </div>
        </div>
    </section>

    {{-- Urgent Requests Section --}}
    <section id="urgent" class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 mt-20">
        <div class="text-center mb-12">
            <span class="text-red-500 font-extrabold text-sm tracking-widest uppercase">জরুরি</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">
                জরুরি <span class="text-red-600">রক্তের প্রয়োজন</span>
            </h2>
            <p class="text-slate-500 mt-3 font-medium">এই রোগীদের এখনই আপনার সাহায্য প্রয়োজন</p>
        </div>

        @php
            $urgentCards = [
                ['name'=>'রহিম উদ্দিন','place'=>'ঢাকা মেডিকেল কলেজ হাসপাতাল','area'=>'সাভার, ঢাকা','bg'=>'O+','time'=>'২ ঘণ্টা আগে'],
                ['name'=>'ফাতেমা বেগম','place'=>'ইবনে সিনা হাসপাতাল','area'=>'গুলশান, ঢাকা','bg'=>'B-','time'=>'৪ ঘণ্টা আগে'],
                ['name'=>'করিম মিয়া','place'=>'চট্টগ্রাম মেডিকেল কলেজ','area'=>'হালিশহর, চট্টগ্রাম','bg'=>'AB+','time'=>'৫ ঘণ্টা আগে'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($urgentCards as $c)
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
                                <span class="inline-block w-2 h-2 rounded-full bg-red-500"></span>{{ $c['area'] }}
                            </div>
                            <div class="flex items-center gap-3 text-slate-500 text-sm font-semibold">
                                <span class="inline-block w-2 h-2 rounded-full bg-slate-300"></span>{{ $c['time'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Donor Feed Section --}}
    <section class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-10 py-20">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">
                আমাদের <span class="text-red-600">সম্মানিত রক্তদাতা</span>
            </h2>
            <p class="text-slate-500 mt-3 font-medium">যারা রক্তদানে প্রস্তুত আছেন</p>
        </div>

        @php
            $donorCards = [
                ['name'=>'তানভীর আহমেদ','district'=>'ঢাকা','upazila'=>'মিরপুর','bg'=>'A+'],
                ['name'=>'সাদিয়া ইসলাম','district'=>'চট্টগ্রাম','upazila'=>'খুলশী','bg'=>'O-'],
                ['name'=>'রাকিবুল হাসান','district'=>'সিলেট','upazila'=>'জিন্দাবাজার','bg'=>'B+'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($donorCards as $donor)
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-red-50 rounded-bl-full z-0 transition group-hover:bg-red-100"></div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-800">{{ $donor['name'] }}</h3>
                                <p class="text-slate-500 text-sm mt-1 font-semibold">
                                    <svg class="w-4 h-4 inline mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                    {{ $donor['upazila'] }}, {{ $donor['district'] }}
                                </p>
                            </div>
                            <div class="bg-red-600 text-white font-black text-lg px-3 py-1.5 rounded-lg shadow-sm">
                                {{ $donor['bg'] }}
                            </div>
                        </div>
                        <div class="mb-5">
                            <span class="inline-flex items-center bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-full font-bold border border-green-200">
                                <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                রক্তদানে প্রস্তুত
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('search') }}" class="inline-flex items-center justify-center border border-slate-300 bg-white text-slate-700 font-extrabold px-7 py-3 rounded-lg hover:bg-slate-50 transition shadow-sm">
                আরও ডোনার খুঁজুন
            </a>
        </div>
    </section>

    <footer class="bg-white border-t border-slate-100 py-8 text-center text-slate-500 font-medium">
        <p>© {{ date('Y') }} রক্তদূত. সর্বস্বত্ব সংরক্ষিত.</p>
    </footer>

    {{-- ⚙️ THE AJAX LOGIC SCRIPT (Optimized) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const divisionSelect = document.getElementById('division_select');
            const districtSelect = document.getElementById('district_select');
            const upazilaSelect = document.getElementById('upazila_select');

            // On Division Change -> Fetch Districts
            divisionSelect.addEventListener('change', function() {
                const divId = this.value;
                districtSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                districtSelect.disabled = true;
                upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                upazilaSelect.disabled = true;

                if (divId) {
                    fetch(`/ajax/districts/${divId}`)
                        .then(res => res.json())
                        .then(data => {
                            districtSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
                            districtSelect.disabled = false;
                            data.forEach(dist => {
                                districtSelect.innerHTML += `<option value="${dist.id}">${dist.name}</option>`;
                            });
                        })
                        .catch(err => console.error("Error fetching districts:", err));
                } else {
                    districtSelect.innerHTML = '<option value="">জেলা নির্বাচন</option>';
                }
            });

            // On District Change -> Fetch Upazilas
            districtSelect.addEventListener('change', function() {
                const distId = this.value;
                upazilaSelect.innerHTML = '<option value="">লোড হচ্ছে...</option>';
                upazilaSelect.disabled = true;

                if (distId) {
                    fetch(`/ajax/upazilas/${distId}`)
                        .then(res => res.json())
                        .then(data => {
                            upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                            upazilaSelect.disabled = false;
                            data.forEach(upz => {
                                upazilaSelect.innerHTML += `<option value="${upz.id}">${upz.name}</option>`;
                            });
                        })
                        .catch(err => console.error("Error fetching upazilas:", err));
                } else {
                    upazilaSelect.innerHTML = '<option value="">উপজেলা/এরিয়া</option>';
                }
            });
        });
    </script>
</body>
</html>