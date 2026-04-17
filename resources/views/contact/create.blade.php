@extends('layouts.app')

@section('title', 'যোগাযোগ করুন — রক্তদূত')

@section('content')

{{-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-800 via-slate-900 to-slate-800">
    <div class="absolute top-0 right-0 w-96 h-96 bg-red-600/10 rounded-full -translate-y-1/2 translate-x-1/3 pointer-events-none blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-red-500/8 rounded-full translate-y-1/2 -translate-x-1/4 pointer-events-none blur-2xl"></div>

    <div class="relative mx-auto max-w-5xl px-4 py-12 sm:py-14">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl">✉️</div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">সাপোর্ট সেন্টার</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight">যোগাযোগ করুন</h1>
        <p class="mt-2 text-slate-400 text-sm font-medium max-w-xl leading-relaxed">
            প্রশ্ন, পরামর্শ বা প্রযুক্তিগত সমস্যা — আমাদের দল সবসময় আপনার পাশে আছে।
        </p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     MAIN LAYOUT — Two-column on desktop
══════════════════════════════════════════════════════════ --}}
<div class="mx-auto max-w-5xl px-4 py-10 lg:py-12">
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-10 items-start">

        {{-- ════════════════════════════════════════════════
             LEFT — Support Info
        ════════════════════════════════════════════════ --}}
        <div class="space-y-7">

            {{-- What to write --}}
            <div>
                <h2 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center text-sm shrink-0">📝</span>
                    কী কী বিষয়ে লিখতে পারেন
                </h2>
                <div class="space-y-2.5">
                    @php
                    $topics = [
                        ['icon' => '🐛', 'title' => 'প্রযুক্তিগত সমস্যা',
                         'desc'  => 'লগইন, ভেরিফিকেশন, ফর্ম বা পেজ লোডের সমস্যা।',
                         'bg'    => 'bg-blue-50 border-blue-100'],
                        ['icon' => '🪪', 'title' => 'NID / পরিচয় যাচাই',
                         'desc'  => 'আপলোড ব্যর্থতা, Verified ব্যাজ না পাওয়া সংক্রান্ত।',
                         'bg'    => 'bg-amber-50 border-amber-100'],
                        ['icon' => '🩸', 'title' => 'রক্তের অনুরোধ সমস্যা',
                         'desc'  => 'অনুরোধ না পৌঁছানো, ডোনার ম্যাচিং সংক্রান্ত।',
                         'bg'    => 'bg-red-50 border-red-100'],
                        ['icon' => '💡', 'title' => 'পরামর্শ ও ফিডব্যাক',
                         'desc'  => 'নতুন ফিচার, UI উন্নতি বা সাধারণ মতামত।',
                         'bg'    => 'bg-emerald-50 border-emerald-100'],
                        ['icon' => '🚩', 'title' => 'অপব্যবহারের রিপোর্ট',
                         'desc'  => 'ভুয়া অ্যাকাউন্ট, হয়রানি বা নিয়ম লঙ্ঘন।',
                         'bg'    => 'bg-rose-50 border-rose-100'],
                    ];
                    @endphp
                    @foreach($topics as $t)
                    <div class="flex items-start gap-3 rounded-xl border {{ $t['bg'] }} p-4">
                        <span class="text-xl shrink-0 leading-none mt-0.5">{{ $t['icon'] }}</span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $t['title'] }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $t['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Response Time --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
                    <span class="text-base">⏱️</span> সম্ভাব্য রেসপন্স সময়
                </h3>
                <div class="space-y-2.5 divide-y divide-slate-100">
                    @php
                    $rtimes = [
                        ['label' => 'সাধারণ অনুসন্ধান',     'time' => '৪৮ ঘণ্টার মধ্যে', 'pill' => 'text-slate-600 bg-slate-100'],
                        ['label' => 'প্রযুক্তিগত সমস্যা',   'time' => '২৪ ঘণ্টার মধ্যে', 'pill' => 'text-blue-700 bg-blue-100'],
                        ['label' => 'অপব্যবহারের রিপোর্ট',  'time' => '৬ ঘণ্টার মধ্যে',  'pill' => 'text-red-700 bg-red-100'],
                    ];
                    @endphp
                    @foreach($rtimes as $rt)
                    <div class="flex items-center justify-between gap-3 py-2 first:pt-0 last:pb-0">
                        <span class="text-sm text-slate-600 font-medium">{{ $rt['label'] }}</span>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $rt['pill'] }} shrink-0">
                            {{ $rt['time'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Emergency notice --}}
            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                <p class="text-sm font-black text-red-800 mb-2 flex items-center gap-2">
                    <span>🚨</span> জরুরি অবস্থায় কী করবেন?
                </p>
                <p class="text-sm text-red-700 leading-relaxed font-medium">
                    রক্তের জরুরি প্রয়োজনে এই ফর্মে নয় — সরাসরি
                    <strong>রক্তের অনুরোধ</strong> করুন অথবা জাতীয় হেল্পলাইন
                    <strong>১৬৪৩০</strong> বা নিকটস্থ হাসপাতালের ব্লাড ব্যাংকে যোগাযোগ করুন।
                </p>
                <a href="{{ route('requests.create') }}"
                   class="mt-3 inline-flex items-center gap-1.5 text-sm font-bold text-red-700 hover:text-red-900 underline underline-offset-2 transition-colors">
                    🩸 রক্তের অনুরোধ করুন →
                </a>
            </div>

        </div>

        {{-- ════════════════════════════════════════════════
             RIGHT — Form Card
        ════════════════════════════════════════════════ --}}
        <div>
            <div class="rounded-2xl border border-slate-200 bg-white shadow-md overflow-hidden"
                 x-data="{
                     loading:   false,
                     charCount: {{ strlen(old('message', '')) }},
                     maxChars:  2000,
                     submitForm(form) {
                         this.loading = true;
                         form.submit();
                     }
                 }">

                {{-- Card header --}}
                <div class="bg-gradient-to-r from-red-600 to-rose-600 px-6 py-5">
                    <p class="text-white font-black text-base flex items-center gap-2">
                        📬 বার্তা পাঠান
                    </p>
                    <p class="text-red-100 text-xs mt-0.5 font-medium">
                        তারকা (<span class="font-black">*</span>) চিহ্নিত ঘরগুলো পূরণ বাধ্যতামূলক।
                    </p>
                </div>

                <div class="px-6 py-6">

                    {{-- Field-level error summary --}}
                    @if($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3.5 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-bold text-red-800 mb-1">ফর্মে কিছু ভুল আছে:</p>
                            <ul class="space-y-0.5 text-xs text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <form id="contact-form"
                          method="POST"
                          action="{{ auth()->check() ? route('contact.store') : route('contact.store.guest') }}"
                          @submit.prevent="submitForm($el)"
                          novalidate
                          class="space-y-4">
                        @csrf

                        {{-- ── Honeypot (hidden from humans) ── --}}
                        <div class="absolute -left-[9999px] -top-[9999px] w-0 h-0 overflow-hidden" aria-hidden="true">
                            <input type="text" name="website" id="website" value="" tabindex="-1" autocomplete="off">
                        </div>

                        {{-- ── Sender info ── --}}
                        @auth
                        {{-- Logged-in: show profile chip --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600 text-sm font-black shrink-0">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shrink-0">Verified</span>
                        </div>
                        <input type="hidden" name="name"  value="{{ auth()->user()->name }}">
                        <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                        @else
                        {{-- Guest: Name + Email fields --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="c-name" class="cf-label">
                                    আপনার নাম <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="c-name" name="name"
                                       value="{{ old('name') }}"
                                       placeholder="পুরো নাম"
                                       autocomplete="name"
                                       class="cf-input @error('name') cf-error @enderror">
                                @error('name')
                                <p class="cf-errmsg">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="c-email" class="cf-label">
                                    ইমেইল <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="c-email" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="you@example.com"
                                       autocomplete="email"
                                       class="cf-input @error('email') cf-error @enderror">
                                @error('email')
                                <p class="cf-errmsg">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        @endauth

                        {{-- ── Phone (optional) ── --}}
                        <div>
                            <label for="c-phone" class="cf-label">
                                ফোন নম্বর
                                <span class="text-slate-400 font-normal">(ঐচ্ছিক)</span>
                            </label>
                            <input type="tel" id="c-phone" name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="যেমন: ০১৭XXXXXXXX"
                                   autocomplete="tel"
                                   class="cf-input @error('phone') cf-error @enderror">
                            @error('phone')
                            <p class="cf-errmsg">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Subject (select) ── --}}
                        <div>
                            <label for="c-subject" class="cf-label">
                                বিষয় <span class="text-red-500">*</span>
                            </label>
                            <select id="c-subject" name="subject"
                                    class="cf-input @error('subject') cf-error @enderror">
                                <option value="" disabled {{ old('subject') ? '' : 'selected' }}>— বিষয় বেছে নিন —</option>
                                @php
                                $subjectOptions = [
                                    'প্রযুক্তিগত সমস্যার রিপোর্ট',
                                    'NID ভেরিফিকেশন সমস্যা',
                                    'রক্তের অনুরোধ সংক্রান্ত',
                                    'অপব্যবহারের রিপোর্ট',
                                    'পরামর্শ ও ফিডব্যাক',
                                    'অন্যান্য বিষয়',
                                ];
                                @endphp
                                @foreach($subjectOptions as $opt)
                                <option value="{{ $opt }}" {{ old('subject') === $opt ? 'selected' : '' }}>
                                    {{ $opt }}
                                </option>
                                @endforeach
                            </select>
                            @error('subject')
                            <p class="cf-errmsg">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ── Message textarea ── --}}
                        <div>
                            <label for="c-message" class="cf-label">
                                বার্তা <span class="text-red-500">*</span>
                            </label>
                            <textarea id="c-message" name="message"
                                      rows="5"
                                      maxlength="2000"
                                      placeholder="আপনার সমস্যা বা বার্তা বিস্তারিত লিখুন... (কমপক্ষে ২০ অক্ষর)"
                                      @input="charCount = $event.target.value.length"
                                      class="cf-input resize-none @error('message') cf-error @enderror">{{ old('message') }}</textarea>
                            <div class="flex items-center justify-between mt-1.5">
                                @error('message')
                                <p class="cf-errmsg flex-1">{{ $message }}</p>
                                @else
                                <span class="flex-1"></span>
                                @enderror
                                <span class="text-xs text-slate-400 font-medium shrink-0"
                                      :class="charCount > 1900 ? 'text-amber-600 font-bold' : ''">
                                    <span x-text="charCount + ' / 2000'">{{ strlen(old('message', '')) }} / 2000</span>
                                </span>
                            </div>
                        </div>

                        {{-- ── Submit Button ── --}}
                        <button type="submit"
                                id="contact-submit-btn"
                                :disabled="loading"
                                :class="loading ? 'opacity-60 cursor-not-allowed' : 'hover:bg-red-700 hover:shadow-md active:scale-[0.98]'"
                                class="w-full flex items-center justify-center gap-2 bg-red-600 text-white font-black text-sm px-6 py-3.5 rounded-xl shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            {{-- Spinner (visible when loading) --}}
                            <svg x-show="loading"
                                 class="w-4 h-4 animate-spin shrink-0"
                                 fill="none" viewBox="0 0 24 24"
                                 style="display:none;" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span x-text="loading ? 'পাঠানো হচ্ছে...' : '📬 বার্তা পাঠান'">📬 বার্তা পাঠান</span>
                        </button>

                        <p class="text-center text-xs text-slate-400 font-medium leading-relaxed">
                            বার্তা পাঠিয়ে আপনি আমাদের
                            <a href="{{ route('privacy') }}" class="underline hover:text-red-600 transition-colors">প্রাইভেসি পলিসি</a>-তে সম্মত হচ্ছেন।
                        </p>
                    </form>
                </div>

            </div>

            {{-- Quick info strip --}}
            <div class="mt-4 grid grid-cols-2 gap-3">
                <a href="mailto:support@roktodut.com"
                   class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 hover:border-red-200 hover:bg-red-50/30 transition-colors shadow-sm group">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-sm shrink-0 group-hover:bg-red-200 transition-colors">✉️</div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-800 truncate">support@roktodut.com</p>
                        <p class="text-[10px] text-slate-500">ইমেইল সাপোর্ট</p>
                    </div>
                </a>
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-sm shrink-0">⏰</div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">শনি – বৃহস্পতি</p>
                        <p class="text-[10px] text-slate-500">সকাল ৯টা – রাত ৯টা</p>
                    </div>
                </div>
            </div>

        </div>{{-- /RIGHT --}}
    </div>{{-- /grid --}}
</div>



@endsection
