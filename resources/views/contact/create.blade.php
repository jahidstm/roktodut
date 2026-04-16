@extends('layouts.app')

@section('title', 'যোগাযোগ করুন — রক্তদূত')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12 sm:py-16">

    {{-- ── পেজ হেডার ──────────────────────────────────────────────────────── --}}
    <div class="mb-10 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-red-50 border border-red-100 mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900">যোগাযোগ করুন</h1>
        <p class="mt-2 text-slate-500 text-sm max-w-md mx-auto leading-relaxed">
            আমাদের সাথে যেকোনো বিষয়ে কথা বলুন। আপনার বার্তা পেলে আমরা শীঘ্রই সাড়া দেব।
        </p>
    </div>

    {{-- ── ফর্ম কার্ড ──────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- ---- ইনফো ব্যানার ---- --}}
        <div class="bg-gradient-to-r from-red-600 to-rose-500 px-6 py-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-white/80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-white text-sm font-semibold">
                সরাসরি প্রশ্ন বা পরামর্শের জন্য এই ফর্ম ব্যবহার করুন। জরুরি রক্তের জন্য
                <a href="{{ route('requests.create') }}" class="underline font-bold hover:text-red-100 transition-colors">রক্তের অনুরোধ পাঠান</a>।
            </p>
        </div>

        <form
            method="POST"
            {{-- Auth হলে contact.store, Guest হলে contact.store.guest --}}
            action="{{ auth()->check() ? route('contact.store') : route('contact.store.guest') }}"
            class="px-6 py-8 space-y-6"
            novalidate
        >
            @csrf

            {{-- ══ HONEYPOT — বট ট্র্যাপ (CSS দিয়ে লুকানো) ══ --}}
            <div class="absolute -left-[9999px] -top-[9999px] overflow-hidden" aria-hidden="true" tabindex="-1">
                <label for="website" class="sr-only">Website (leave blank)</label>
                <input
                    type="text"
                    id="website"
                    name="website"
                    value=""
                    autocomplete="off"
                    tabindex="-1"
                >
            </div>
            {{-- ══════════════════════════════════════════════════ --}}

            {{-- ── প্রথম সারি: নাম + ইমেইল ──────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- নাম --}}
                <div>
                    <label for="contact-name" class="block text-sm font-bold text-slate-700 mb-1.5">
                        নাম
                        @guest<span class="text-red-500 ml-0.5">*</span>@endguest
                    </label>
                    @auth
                        <input
                            type="text"
                            id="contact-name"
                            name="name"
                            value="{{ auth()->user()->name }}"
                            readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed"
                        >
                        <p class="mt-1 text-xs text-slate-400">আপনার প্রোফাইলের নাম ব্যবহার করা হবে।</p>
                    @else
                        <input
                            type="text"
                            id="contact-name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="আপনার পুরো নাম"
                            maxlength="120"
                            class="w-full rounded-xl border @error('name') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-colors"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    @endauth
                </div>

                {{-- ইমেইল --}}
                <div>
                    <label for="contact-email" class="block text-sm font-bold text-slate-700 mb-1.5">
                        ইমেইল ঠিকানা <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    @auth
                        <input
                            type="email"
                            id="contact-email"
                            name="email"
                            value="{{ auth()->user()->email }}"
                            readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed"
                        >
                        <p class="mt-1 text-xs text-slate-400">আপনার অ্যাকাউন্টের ইমেইল ব্যবহার হবে।</p>
                    @else
                        <input
                            type="email"
                            id="contact-email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="yourname@example.com"
                            maxlength="180"
                            class="w-full rounded-xl border @error('email') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-colors"
                        >
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                        @enderror
                    @endauth
                </div>
            </div>

            {{-- ── ফোন ──────────────────────────────────────────── --}}
            <div>
                <label for="contact-phone" class="block text-sm font-bold text-slate-700 mb-1.5">
                    ফোন নম্বর <span class="text-slate-400 font-normal text-xs">(ঐচ্ছিক)</span>
                </label>
                <input
                    type="tel"
                    id="contact-phone"
                    name="phone"
                    value="{{ old('phone', auth()->user()?->phone) }}"
                    placeholder="+880 1XXXXXXXXX"
                    maxlength="20"
                    class="w-full rounded-xl border @error('phone') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-colors"
                >
                @error('phone')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── বিষয় ─────────────────────────────────────────── --}}
            <div>
                <label for="contact-subject" class="block text-sm font-bold text-slate-700 mb-1.5">
                    বিষয় <span class="text-red-500 ml-0.5">*</span>
                </label>
                <input
                    type="text"
                    id="contact-subject"
                    name="subject"
                    value="{{ old('subject') }}"
                    placeholder="আপনার বার্তার বিষয়"
                    minlength="5"
                    maxlength="120"
                    class="w-full rounded-xl border @error('subject') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-colors"
                >
                @error('subject')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── মূল বার্তা ──────────────────────────────────────── --}}
            <div>
                <div class="flex items-baseline justify-between mb-1.5">
                    <label for="contact-message" class="text-sm font-bold text-slate-700">
                        বার্তা <span class="text-red-500 ml-0.5">*</span>
                    </label>
                    <span id="char-count" class="text-xs text-slate-400 font-medium tabular-nums">০/২০০০</span>
                </div>
                <textarea
                    id="contact-message"
                    name="message"
                    rows="6"
                    minlength="20"
                    maxlength="2000"
                    placeholder="আপনার বার্তা এখানে লিখুন... (কমপক্ষে ২০ অক্ষর)"
                    oninput="document.getElementById('char-count').textContent = this.value.length + '/২০০০'"
                    class="w-full rounded-xl border @error('message') border-red-400 bg-red-50 @else border-slate-200 bg-white @enderror px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-colors resize-none leading-relaxed"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- ── সাবমিট বাটন ─────────────────────────────────────── --}}
            <div class="pt-2">
                <button
                    type="submit"
                    id="contact-submit-btn"
                    class="w-full flex items-center justify-center gap-2.5 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white font-extrabold text-base px-6 py-3.5 rounded-xl shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    বার্তা পাঠান
                </button>
                <p class="mt-3 text-center text-xs text-slate-400">
                    আপনার তথ্য সম্পূর্ণ গোপনীয় থাকবে। আমরা সাধারণত ২৪ ঘণ্টার মধ্যে সাড়া দিই।
                </p>
            </div>

        </form>
    </div>

    {{-- ── যোগাযোগের তথ্য কার্ড ────────────────────────────────────── --}}
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-start gap-3">
            <div class="shrink-0 w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">ইমেইল</p>
                <p class="text-sm font-semibold text-slate-800 mt-0.5">support@roktodut.com</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-start gap-3">
            <div class="shrink-0 w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">সাড়া দেওয়ার সময়</p>
                <p class="text-sm font-semibold text-slate-800 mt-0.5">সাধারণত ২৪ ঘণ্টার মধ্যে</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-start gap-3">
            <div class="shrink-0 w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">ভাষা</p>
                <p class="text-sm font-semibold text-slate-800 mt-0.5">বাংলা ও ইংরেজি</p>
            </div>
        </div>
    </div>

</div>
@endsection
