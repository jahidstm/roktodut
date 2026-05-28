{{-- ══════════════════════════════════════════════════════
     FOOTER — 4-Column Dark Footer with Distinct Bottom Bar
     Routes verified against routes/web.php
══════════════════════════════════════════════════════ --}}
<footer class="bg-slate-950">

    {{-- ══ Main Columns Area ══════════════════════════════════════════ --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-12 lg:pt-16 pb-10 lg:pb-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-12 lg:gap-8 xl:gap-12">

            {{-- Column 1: Brand & Trust --}}
            <div class="space-y-5 sm:col-span-2 lg:col-span-2 lg:pr-12 flex flex-col items-center sm:items-start text-center sm:text-left mb-2 lg:mb-0">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <div class="h-10 w-10 rounded-xl bg-white flex items-center justify-center shrink-0 overflow-hidden shadow-sm">
                        <img src="{{ asset('images/image_14.png') }}" alt="RoktoDut" class="w-full h-full object-contain p-1">
                    </div>
                    <span class="text-xl font-extrabold text-white tracking-tight">রক্তদূত</span>
                </a>
                <p class="text-sm text-slate-400 font-medium leading-relaxed max-w-md">
                    জরুরি মুহূর্তে রক্তের সন্ধানে বাংলাদেশের সবচেয়ে নির্ভরযোগ্য ভেরিফায়েড <br> প্ল্যাটফর্ম। এক ফোঁটা রক্ত, বাঁচাতে পারে একটি প্রাণ।
                </p>
                {{-- Contact Info --}}
                <div class="pt-2 flex flex-row flex-wrap xl:flex-nowrap items-center gap-3 w-full">
                    <a href="tel:16263" class="flex-1 min-w-[140px] inline-flex items-center gap-2.5 p-2 rounded-xl bg-slate-900/50 border border-slate-800 hover:bg-slate-800 hover:border-slate-700 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[9px] text-slate-500 font-extrabold uppercase tracking-widest mb-0.5">হটলাইন</p>
                            <p class="text-[11px] sm:text-xs font-black text-slate-200 group-hover:text-white transition-colors">১৬২৬৩</p>
                        </div>
                    </a>
                    <a href="mailto:support@roktodut.com" class="flex-1 min-w-[140px] inline-flex items-center gap-2.5 p-2 rounded-xl bg-slate-900/50 border border-slate-800 hover:bg-slate-800 hover:border-slate-700 transition-all group">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-blue-500 group-hover:text-white transition-all shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[9px] text-slate-500 font-extrabold uppercase tracking-widest mb-0.5">সাপোর্ট</p>
                            <p class="text-[11px] sm:text-xs font-black text-slate-200 group-hover:text-white transition-colors">ইমেইল করুন</p>
                        </div>
                    </a>
                </div>

                {{-- Social Icons --}}
                <div class="flex items-center justify-center sm:justify-start gap-4 pt-3 w-full">
                    <a href="#" aria-label="Facebook" class="text-slate-500 hover:text-[#1877F2] transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter / X" class="text-slate-500 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="text-slate-500 hover:text-[#E4405F] transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="text-slate-500 hover:text-[#0A66C2] transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Column 2: Quick Links (B2C) --}}
            <div class="sm:justify-self-center lg:justify-self-start">
                <h3 class="text-[11px] sm:text-xs font-extrabold text-white uppercase tracking-widest mb-4 sm:mb-5">কুইক লিংকস</h3>
                <ul class="space-y-3.5">
                    <li>
                        <a href="{{ route('search') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            রক্তদাতা খুঁজুন
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.requests.index') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            রক্তের অনুরোধ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('live-demand.index') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            লাইভ রক্ত চাহিদা
                            <span class="relative flex h-2 w-2 ml-1">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ambulances.index') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            অ্যাম্বুলেন্স সার্ভিস
                            <span class="bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full ml-1">NEW</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leaderboard') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            লিডারবোর্ড
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog.index') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            স্বাস্থ্যবার্তা
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Organizations (B2B) --}}
            <div class="sm:justify-self-center lg:justify-self-start">
                <h3 class="text-[11px] sm:text-xs font-extrabold text-white uppercase tracking-widest mb-4 sm:mb-5">অর্গানাইজেশন</h3>
                <ul class="space-y-3.5">
                    <li>
                        <a href="{{ route('org.register') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            <span>ক্লাব/হাসপাতাল রেজিস্ট্রেশন</span>
                            <span class="animate-pulse bg-red-500/20 text-red-400 text-[9px] font-black uppercase px-1.5 py-0.5 rounded-full border border-red-500/30 group-hover:bg-red-600 group-hover:text-white group-hover:border-red-600 transition-all leading-none">New</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            অর্গানাইজেশন লগইন
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('gamification.guide') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            ভেরিফিকেশন নীতিমালা
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leaderboard') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            ভেরিফাইড ডোনার তালিকা
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            রক্তদান ক্যাম্পেইন
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 4: Support & Legal --}}
            <div class="sm:justify-self-center lg:justify-self-start">
                <h3 class="text-[11px] sm:text-xs font-extrabold text-white uppercase tracking-widest mb-4 sm:mb-5">সাপোর্ট ও লিগ্যাল</h3>
                <ul class="space-y-3.5">
                    <li>
                        <a href="{{ route('about') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            আমাদের সম্পর্কে
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact.create') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            কন্টাক্ট করুন
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            প্রাইভেসি পলিসি
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            ব্যবহারের শর্তাবলী
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white hover:translate-x-1.5 transition-all font-medium">
                            সচরাচর জিজ্ঞাসা (FAQ)
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- ══ Bottom Bar — Visually distinct (darker + border) ════════════ --}}
    <div class="bg-black/40 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 sm:py-5 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-2">
            <p class="text-[11px] text-white/70 font-medium text-center sm:text-left">
                &copy; {{ date('Y') }} রক্তদূত প্ল্যাটফর্ম। সর্বস্বত্ব সংরক্ষিত।
            </p>
            <p class="text-[11px] text-white/70 font-medium flex items-center justify-center gap-1">
                Made with <span class="text-red-500 mx-0.5">❤️</span> in Bangladesh
            </p>
        </div>
    </div>

</footer>
