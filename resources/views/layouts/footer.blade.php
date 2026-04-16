{{-- ══════════════════════════════════════════════════════
     FOOTER — 4-Column Dark Footer with Distinct Bottom Bar
     Routes verified against routes/web.php
══════════════════════════════════════════════════════ --}}
<footer class="bg-slate-950">

    {{-- ══ Main Columns Area ══════════════════════════════════════════ --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-16 xl:gap-24">

            {{-- Column 1: Brand & Trust --}}
            <div class="space-y-5 sm:col-span-2 lg:col-span-1 lg:pr-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-red-600 flex items-center justify-center shrink-0">
                        <span class="text-white font-extrabold tracking-tight text-sm">RD</span>
                    </div>
                    <span class="text-xl font-extrabold text-white tracking-tight">রক্তদূত</span>
                </a>
                <p class="text-sm text-slate-400 font-medium leading-relaxed">
                    জরুরি মুহূর্তে রক্তের সন্ধানে বাংলাদেশের সবচেয়ে নির্ভরযোগ্য ভেরিফায়েড প্ল্যাটফর্ম। এক ফোঁটা রক্ত, বাঁচাতে পারে একটি প্রাণ।
                </p>
                {{-- Social Icons --}}
                <div class="flex items-center gap-4 pt-1">
                    <a href="#" aria-label="Facebook" class="text-slate-500 hover:text-red-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-label="Twitter / X" class="text-slate-500 hover:text-red-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="text-slate-500 hover:text-red-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" aria-label="LinkedIn" class="text-slate-500 hover:text-red-400 transition-colors">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Column 2: Quick Links (B2C) — exactly 4 items --}}
            <div>
                <h3 class="text-xs font-extrabold text-white uppercase tracking-widest mb-5">কুইক লিংকস</h3>
                <ul class="space-y-3.5">
                    <li>
                        <a href="{{ route('search') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            স্মার্ট ডোনার সার্চ
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('public.requests.index') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            রক্ত দিন
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leaderboard') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            লিডারবোর্ড
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog.index') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            ব্লগ
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Organizations (B2B) — exactly 4 items --}}
            {{-- Routes: org.register ✅, login ✅, gamification.guide ✅ (reused as guide) --}}
            <div>
                <h3 class="text-xs font-extrabold text-white uppercase tracking-widest mb-5">অর্গানাইজেশন</h3>
                <ul class="space-y-3.5">
                    <li>
                        <a href="{{ route('org.register') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            <span>ক্লাব/হাসপাতাল রেজিস্ট্রেশন</span>
                            <span class="bg-red-500/20 text-red-400 text-[9px] font-black uppercase px-1.5 py-0.5 rounded-full border border-red-500/30 group-hover:bg-red-600 group-hover:text-white group-hover:border-red-600 transition-all leading-none">New</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            অর্গানাইজেশন লগইন
                        </a>
                    </li>
                    <li>
                        {{-- Using gamification.guide as the "platform guide/policy" page --}}
                        <a href="{{ route('gamification.guide') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            ভেরিফিকেশন নীতিমালা
                        </a>
                    </li>
                    <li>
                        {{-- No dedicated "verified orgs" list route — link to leaderboard as social proof --}}
                        <a href="{{ route('leaderboard') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            ভেরিফাইড ডোনার তালিকা
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 4: Support & Legal --}}
            <div>
                <h3 class="text-xs font-extrabold text-white uppercase tracking-widest mb-5">সাপোর্ট ও লিগ্যাল</h3>
                <ul class="space-y-3.5">
                    <li>
                        <a href="{{ route('about') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            আমাদের সম্পর্কে
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact.create') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            যোগাযোগ করুন
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            প্রাইভেসি পলিসি
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms') }}" class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors font-medium">
                            ব্যবহারের শর্তাবলী
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- ══ Bottom Bar — Visually distinct (darker + border) ════════════ --}}
    <div class="bg-black/40 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-[11px] text-white font-medium">
                &copy; {{ date('Y') }} রক্তদূত প্ল্যাটফর্ম। সর্বস্বত্ব সংরক্ষিত।
            </p>
            <p class="text-[11px] text-white font-medium flex items-center gap-1">
                Made with <span class="text-red-500 mx-0.5">❤️</span> in Bangladesh
            </p>
        </div>
    </div>

</footer>
