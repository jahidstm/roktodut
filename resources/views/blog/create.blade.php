@extends('layouts.app')

@section('title', 'নতুন পোস্ট লিখুন | রক্তদূত স্বাস্থ্যবার্তা')

{{-- ═══════════════════════════════════════════════════════════════════════
     BLOG CREATE — Quill.js WYSIWYG Submission Form
     PRD: EXIF-stripped, WebP-compressed cover image; story privacy gate;
          anonymization enum; optional verified-story link
     ═══════════════════════════════════════════════════════════════════════ --}}

@push('head')
    {{-- Quill.js CDN (snow theme) --}}
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js" defer></script>
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────────────────────────── --}}
<section class="relative bg-gradient-to-br from-red-700 via-red-600 to-rose-500 overflow-hidden">
    <div class="absolute inset-0 opacity-10"
         style="background-image: linear-gradient(rgba(255,255,255,.15) 1px, transparent 1px),
                                  linear-gradient(90deg,rgba(255,255,255,.15) 1px, transparent 1px);
                background-size: 28px 28px;"></div>
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative mx-auto max-w-4xl px-4 sm:px-6 py-12 md:py-16">
        <a href="{{ route('blog.index') }}"
           class="inline-flex items-center gap-2 text-white/70 hover:text-white text-sm font-semibold mb-6 transition-colors duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            স্বাস্থ্যবার্তায় ফিরুন
        </a>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
            ✍️ নতুন পোস্ট লিখুন
        </h1>
        <p class="mt-3 text-white/75 text-base font-medium max-w-xl">
            আপনার অভিজ্ঞতা বা স্বাস্থ্য তথ্য শেয়ার করুন। পোস্টটি প্রকাশের আগে আমাদের মডারেটর পর্যালোচনা করবেন।
        </p>
    </div>
</section>

{{-- ── Form ────────────────────────────────────────────────────────────── --}}
<div class="mx-auto max-w-4xl px-4 sm:px-6 py-10 lg:py-14">

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div id="validation-error-box"
             class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-5">
            <p class="font-extrabold text-red-700 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                দয়া করে নিচের ত্রুটিগুলো ঠিক করুন:
            </p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-red-600 text-sm font-semibold">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        id="blog-create-form"
        action="{{ route('blog.store') }}"
        method="POST"
        enctype="multipart/form-data"
        novalidate
        class="space-y-8">

        @csrf

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  SECTION 1 — Post Type & Basic Info                          ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">

            <h2 class="text-lg font-extrabold text-slate-900 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-sm font-black">১</span>
                মূল তথ্য
            </h2>

            {{-- Post Type --}}
            <div class="mb-6">
                <label class="block text-sm font-extrabold text-slate-700 mb-3">
                    পোস্টের ধরন <span class="text-red-500">*</span>
                </label>
                <div id="post-type-group" class="grid grid-cols-2 gap-4" role="group" aria-label="পোস্টের ধরন নির্বাচন করুন">
                    {{-- Health Blog --}}
                    <label id="type-health-label"
                           class="type-option group cursor-pointer rounded-xl border-2 p-4 flex items-start gap-3 transition-all duration-200
                                  {{ old('type', 'health') === 'health' ? 'border-sky-500 bg-sky-50' : 'border-slate-200 hover:border-sky-300 hover:bg-sky-50/50' }}">
                        <input type="radio" name="type" id="type-health" value="health"
                               class="sr-only type-radio"
                               {{ old('type', 'health') === 'health' ? 'checked' : '' }}>
                        <span class="text-2xl shrink-0">🏥</span>
                        <div>
                            <div class="font-extrabold text-slate-800 text-sm">স্বাস্থ্য ব্লগ</div>
                            <div class="text-xs text-slate-500 font-medium mt-0.5 leading-relaxed">চিকিৎসা তথ্য, টিপস এবং স্বাস্থ্য সচেতনতামূলক কন্টেন্ট</div>
                        </div>
                    </label>

                    {{-- Success Story --}}
                    <label id="type-story-label"
                           class="type-option group cursor-pointer rounded-xl border-2 p-4 flex items-start gap-3 transition-all duration-200
                                  {{ old('type') === 'story' ? 'border-rose-500 bg-rose-50' : 'border-slate-200 hover:border-rose-300 hover:bg-rose-50/50' }}">
                        <input type="radio" name="type" id="type-story" value="story"
                               class="sr-only type-radio"
                               {{ old('type') === 'story' ? 'checked' : '' }}>
                        <span class="text-2xl shrink-0">💪</span>
                        <div>
                            <div class="font-extrabold text-slate-800 text-sm">সাফল্যের গল্প</div>
                            <div class="text-xs text-slate-500 font-medium mt-0.5 leading-relaxed">রক্তদানের অভিজ্ঞতা ও অনুপ্রেরণার গল্প</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Title --}}
            <div class="mb-6">
                <label for="post-title"
                       class="block text-sm font-extrabold text-slate-700 mb-1.5">
                    পোস্টের শিরোনাম <span class="text-red-500">*</span>
                </label>
                <input
                    id="post-title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    maxlength="200"
                    placeholder="আপনার পোস্টের একটি আকর্ষণীয় শিরোনাম লিখুন…"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-semibold text-base
                           placeholder:text-slate-400 placeholder:font-normal
                           focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-all duration-200"
                    aria-describedby="title-hint">
                <p id="title-hint" class="mt-1.5 text-xs text-slate-400 font-medium">সর্বোচ্চ ২০০ অক্ষর</p>
            </div>

            {{-- Excerpt --}}
            <div class="mb-6">
                <label for="post-excerpt"
                       class="block text-sm font-extrabold text-slate-700 mb-1.5">
                    সংক্ষিপ্ত বিবরণ
                    <span class="text-slate-400 font-normal text-xs ml-1">(ঐচ্ছিক — না দিলে স্বয়ংক্রিয়ভাবে তৈরি হবে)</span>
                </label>
                <textarea
                    id="post-excerpt"
                    name="excerpt"
                    rows="2"
                    maxlength="500"
                    placeholder="পোস্টের একটি সংক্ষিপ্ত সারসংক্ষেপ যা কার্ডে দেখাবে…"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium text-sm
                           placeholder:text-slate-400 placeholder:font-normal resize-none
                           focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-all duration-200">{{ old('excerpt') }}</textarea>
            </div>

            {{-- Cover Image --}}
            <div>
                <label for="cover-image"
                       class="block text-sm font-extrabold text-slate-700 mb-1.5">
                    কভার ইমেজ
                    <span class="text-slate-400 font-normal text-xs ml-1">(ঐচ্ছিক — সর্বোচ্চ ২ MB, JPG/PNG/WebP)</span>
                </label>
                <div id="cover-image-drop-zone"
                     class="relative border-2 border-dashed border-slate-200 rounded-xl bg-slate-50 hover:border-red-300 hover:bg-red-50/30 transition-all duration-200 cursor-pointer group"
                     role="button"
                     tabindex="0"
                     aria-label="কভার ইমেজ আপলোড করুন">
                    <div id="cover-image-placeholder" class="flex flex-col items-center justify-center py-10 px-4 text-center">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 group-hover:bg-red-100 flex items-center justify-center mb-3 transition-colors duration-200">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-red-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-600 group-hover:text-red-600 transition-colors duration-200">
                            ক্লিক করুন বা ছবি ড্র্যাগ করুন
                        </p>
                        <p class="text-xs text-slate-400 font-medium mt-1">
                            ইমেজ স্বয়ংক্রিয়ভাবে WebP-তে রূপান্তরিত হবে এবং EXIF/লোকেশন ডেটা মুছে যাবে
                        </p>
                    </div>
                    <img id="cover-image-preview"
                         src="#"
                         alt="কভার ইমেজ প্রিভিউ"
                         class="hidden w-full max-h-64 object-cover rounded-xl">
                    <input
                        type="file"
                        id="cover-image"
                        name="cover_image"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        aria-label="কভার ইমেজ ফাইল নির্বাচন করুন">
                </div>
                <p id="cover-image-name" class="mt-1.5 text-xs text-slate-500 font-medium hidden"></p>
                @error('cover_image')
                    <p class="mt-1.5 text-xs text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

        </div>{{-- /section-1 --}}


        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  SECTION 2 — WYSIWYG Body Editor (Quill.js)                  ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-extrabold text-slate-900 mb-6 pb-4 border-b border-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 bg-red-100 text-red-600 rounded-lg flex items-center justify-center text-sm font-black">২</span>
                পোস্টের বিষয়বস্তু <span class="text-red-500 font-normal text-sm ml-1">*</span>
            </h2>

            {{-- Quill Toolbar + Editor --}}
            <div id="quill-toolbar" class="rounded-t-xl border border-slate-200 border-b-0">
                <span class="ql-formats">
                    <select class="ql-header" aria-label="Heading level">
                        <option value="1">শিরোনাম ১</option>
                        <option value="2">শিরোনাম ২</option>
                        <option value="3">শিরোনাম ৩</option>
                        <option selected>স্বাভাবিক</option>
                    </select>
                </span>
                <span class="ql-formats">
                    <button class="ql-bold" aria-label="Bold"></button>
                    <button class="ql-italic" aria-label="Italic"></button>
                    <button class="ql-underline" aria-label="Underline"></button>
                    <button class="ql-strike" aria-label="Strikethrough"></button>
                </span>
                <span class="ql-formats">
                    <button class="ql-blockquote" aria-label="Blockquote"></button>
                    <button class="ql-list" value="ordered" aria-label="Ordered list"></button>
                    <button class="ql-list" value="bullet" aria-label="Bullet list"></button>
                </span>
                <span class="ql-formats">
                    <button class="ql-link" aria-label="Insert link"></button>
                    <button class="ql-clean" aria-label="Remove formatting"></button>
                </span>
            </div>
            <div id="quill-editor"
                 class="rounded-b-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium text-base leading-relaxed"
                 style="min-height: 280px; max-height: 600px; overflow-y: auto;"
                 aria-label="পোস্টের মূল বিষয়বস্তু সম্পাদনা করুন">
                {!! old('body_raw') !!}
            </div>

            {{-- Hidden input that Quill syncs to --}}
            <input type="hidden" id="body-raw-input" name="body_raw">

            @error('body_raw')
                <p class="mt-2 text-xs text-red-600 font-semibold">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-400 font-medium">
                💡 টিপ: যেকোনো ব্রাউজার থেকে কপি করে পেস্ট করা টেক্সট সরাসরি ব্যবহার করতে পারেন।
            </p>
        </div>{{-- /section-2 --}}


        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  SECTION 3 — Success Story Meta (conditionally shown)         ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div
            id="story-meta-section"
            class="{{ old('type', 'health') !== 'story' ? 'hidden' : '' }} bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl border border-rose-100 shadow-sm p-6 sm:p-8"
            aria-live="polite">

            <h2 class="text-lg font-extrabold text-rose-800 mb-1 flex items-center gap-2">
                <span class="w-7 h-7 bg-rose-200 text-rose-700 rounded-lg flex items-center justify-center text-sm font-black">৩</span>
                সাফল্যের গল্পের তথ্য
            </h2>
            <p class="text-sm text-rose-600 font-medium mb-6">
                রোগীর গোপনীয়তা রক্ষা বাধ্যতামূলক। নিচের তথ্যগুলো সঠিকভাবে পূরণ করুন।
            </p>

            {{-- ── 3a. Anonymization Level ── --}}
            <div class="mb-6">
                <label for="anonymize-level"
                       class="block text-sm font-extrabold text-rose-800 mb-1.5">
                    লেখক পরিচয় প্রকাশের ধরন <span class="text-red-500">*</span>
                </label>
                <select
                    id="anonymize-level"
                    name="anonymize_level"
                    class="w-full px-4 py-3 rounded-xl border border-rose-200 bg-white text-slate-900 font-semibold text-sm
                           focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-200 cursor-pointer"
                    aria-describedby="anonymize-hint">
                    {{-- Maps EXACTLY to DB enum: public | initials | anonymous --}}
                    <option value="public"    {{ old('anonymize_level', 'public') === 'public'    ? 'selected' : '' }}>
                        নিজ নামে প্রকাশ
                    </option>
                    <option value="initials"  {{ old('anonymize_level') === 'initials'  ? 'selected' : '' }}>
                        শুধুমাত্র নামের আদ্যক্ষর (যেমন: M. H.)
                    </option>
                    <option value="anonymous" {{ old('anonymize_level') === 'anonymous' ? 'selected' : '' }}>
                        পরিচয় গোপন রাখুন (একজন রক্তদাতা হিসেবে প্রকাশ)
                    </option>
                </select>
                <p id="anonymize-hint" class="mt-1.5 text-xs text-rose-500 font-medium">
                    এই সিদ্ধান্ত পরে পরিবর্তন করা যাবে না।
                </p>
            </div>

            {{-- ── 3b. District ── --}}
            <div class="mb-6">
                <label for="story-district"
                       class="block text-sm font-extrabold text-rose-800 mb-1.5">
                    ঘটনার জেলা
                    <span class="text-slate-400 font-normal text-xs ml-1">(ঐচ্ছিক)</span>
                </label>
                <input
                    type="text"
                    id="story-district"
                    name="district"
                    value="{{ old('district') }}"
                    maxlength="100"
                    placeholder="যেমন: ঢাকা, চট্টগ্রাম, রাজশাহী…"
                    class="w-full px-4 py-3 rounded-xl border border-rose-200 bg-white text-slate-900 font-semibold text-sm
                           placeholder:text-slate-400 placeholder:font-normal
                           focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-200">
            </div>

            {{-- ── 3c. Verified Story Link ── --}}
            <div class="mb-6">
                <label for="donation-ref-id"
                       class="block text-sm font-extrabold text-rose-800 mb-1.5">
                    যাচাইযোগ্য গল্পের রেফারেন্স
                    <span class="text-slate-400 font-normal text-xs ml-1">(ঐচ্ছিক — অ্যাডমিন ভেরিফাইড ব্যাজ পেতে)</span>
                </label>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="donation-ref-type" class="text-xs font-bold text-rose-700 mb-1 block">রেকর্ডের ধরন</label>
                        <select
                            id="donation-ref-type"
                            name="donation_ref_type"
                            class="w-full px-4 py-3 rounded-xl border border-rose-200 bg-white text-slate-900 font-semibold text-sm
                                   focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-200 cursor-pointer">
                            <option value="" {{ old('donation_ref_type', '') === '' ? 'selected' : '' }}>-- নির্বাচন করুন --</option>
                            <option value="donation"      {{ old('donation_ref_type') === 'donation'      ? 'selected' : '' }}>ডোনেশন রেকর্ড (Donation ID)</option>
                            <option value="blood_request" {{ old('donation_ref_type') === 'blood_request' ? 'selected' : '' }}>রক্তের অনুরোধ (Request ID)</option>
                        </select>
                    </div>
                    <div>
                        <label for="donation-ref-id" class="text-xs font-bold text-rose-700 mb-1 block">রেকর্ড ID নম্বর</label>
                        <input
                            type="number"
                            id="donation-ref-id"
                            name="donation_ref_id"
                            value="{{ old('donation_ref_id') }}"
                            min="1"
                            placeholder="যেমন: 42"
                            class="w-full px-4 py-3 rounded-xl border border-rose-200 bg-white text-slate-900 font-semibold text-sm
                                   placeholder:text-slate-400 placeholder:font-normal
                                   focus:outline-none focus:ring-2 focus:ring-rose-400 focus:border-transparent transition-all duration-200">
                    </div>
                </div>
                <p class="mt-2 text-xs text-rose-500 font-medium leading-snug">
                    📌 রক্তদূত প্ল্যাটফর্মে সফলভাবে সম্পন্ন হওয়া ডোনেশন বা রক্তের অনুরোধের ID দিন যাতে অ্যাডমিন আপনার গল্পটি ভেরিফাই করতে পারেন।
                </p>
            </div>

            {{-- ── 3d. Privacy Compliance Checkboxes (Required for type=story) ── --}}
            <fieldset class="mt-4 pt-5 border-t border-rose-200">
                <legend class="text-sm font-extrabold text-rose-800 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    গোপনীয়তা নিশ্চিতকরণ <span class="text-red-500">*</span>
                </legend>

                {{-- Checkbox 1: Patient Consent --}}
                <div class="mb-4">
                    <label id="consent-patient-label"
                           class="flex items-start gap-3 cursor-pointer group
                                  p-4 rounded-xl border-2 transition-all duration-200
                                  {{ old('consent_patient') ? 'border-emerald-400 bg-emerald-50' : 'border-rose-200 bg-white hover:border-rose-400' }}">
                        <input
                            type="checkbox"
                            id="consent-patient"
                            name="consent_patient"
                            value="1"
                            {{ old('consent_patient') ? 'checked' : '' }}
                            class="mt-0.5 w-5 h-5 rounded border-rose-300 text-emerald-500 shrink-0
                                   focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1 cursor-pointer
                                   accent-emerald-500">
                        <span class="text-sm font-semibold text-slate-700 leading-snug">
                            ✅ <strong>আমি রোগীর সম্মতি নিয়েছি</strong> — রোগী বা তার আইনি অভিভাবক এই গল্পটি প্রকাশ করার জন্য সম্মতি দিয়েছেন।
                        </span>
                    </label>
                    @error('consent_patient')
                        <p class="mt-1 text-xs text-red-600 font-semibold pl-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Checkbox 2: No Address or Phone --}}
                <div>
                    <label id="consent-no-pii-label"
                           class="flex items-start gap-3 cursor-pointer group
                                  p-4 rounded-xl border-2 transition-all duration-200
                                  {{ old('consent_no_pii') ? 'border-emerald-400 bg-emerald-50' : 'border-rose-200 bg-white hover:border-rose-400' }}">
                        <input
                            type="checkbox"
                            id="consent-no-pii"
                            name="consent_no_pii"
                            value="1"
                            {{ old('consent_no_pii') ? 'checked' : '' }}
                            class="mt-0.5 w-5 h-5 rounded border-rose-300 text-emerald-500 shrink-0
                                   focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1 cursor-pointer
                                   accent-emerald-500">
                        <span class="text-sm font-semibold text-slate-700 leading-snug">
                            🔒 <strong>আমি কোনো ঠিকানা/ফোন নম্বর প্রকাশ করিনি</strong> — এই পোস্টে কোনো বাসার ঠিকানা, ফোন নম্বর বা অন্যান্য ব্যক্তিগত পরিচয় তথ্য নেই।
                        </span>
                    </label>
                    @error('consent_no_pii')
                        <p class="mt-1 text-xs text-red-600 font-semibold pl-1">{{ $message }}</p>
                    @enderror
                </div>
            </fieldset>

        </div>{{-- /story-meta-section --}}


        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  SECTION 4 — Submit                                           ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">

            {{-- Moderation Info Banner --}}
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-amber-800 font-semibold leading-snug">
                    জমা দেওয়ার পরে পোস্টটি <strong>পেন্ডিং রিভিউ</strong> অবস্থায় থাকবে। আমাদের মডারেটর পর্যালোচনা করে অনুমোদন দিলে তা প্রকাশিত হবে।
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button
                    type="submit"
                    id="submit-post-btn"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl
                           bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700
                           text-white font-extrabold text-base shadow-md hover:shadow-lg
                           focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2
                           transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    পর্যালোচনার জন্য জমা দিন
                </button>
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl
                          bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-base
                          focus:outline-none focus:ring-2 focus:ring-slate-300 focus:ring-offset-2
                          transition-all duration-200">
                    বাতিল করুন
                </a>
            </div>
        </div>{{-- /section-4 --}}

    </form>
</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Post Type Toggle ──────────────────────────────────────────────
    const typeRadios       = document.querySelectorAll('.type-radio');
    const storyMetaSection = document.getElementById('story-meta-section');
    const healthLabel      = document.getElementById('type-health-label');
    const storyLabel       = document.getElementById('type-story-label');

    // Story meta required fields (only active when type=story)
    const storyRequiredIds = ['consent-patient', 'consent-no-pii'];

    function updateTypeUI() {
        const isStory = document.getElementById('type-story').checked;

        // Toggle story-meta panel visibility
        storyMetaSection.classList.toggle('hidden', !isStory);

        // Update label styles
        healthLabel.className = healthLabel.className
            .replace(/border-(sky|slate)-\S+/g, '')
            .replace(/bg-(sky|slate)-\S+/g, '');
        storyLabel.className = storyLabel.className
            .replace(/border-(rose|slate)-\S+/g, '')
            .replace(/bg-(rose|slate)-\S+/g, '');

        if (isStory) {
            storyLabel.classList.add('border-rose-500', 'bg-rose-50');
            healthLabel.classList.add('border-slate-200', 'hover:border-sky-300');
        } else {
            healthLabel.classList.add('border-sky-500', 'bg-sky-50');
            storyLabel.classList.add('border-slate-200', 'hover:border-rose-300');
        }

        // Toggle required attribute on checkboxes
        storyRequiredIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.required = isStory;
        });
    }

    typeRadios.forEach(radio => radio.addEventListener('change', updateTypeUI));
    updateTypeUI(); // run on load


    // ── 2. Privacy Checkbox Visual Feedback ─────────────────────────────
    ['consent-patient', 'consent-no-pii'].forEach(id => {
        const checkbox = document.getElementById(id);
        if (!checkbox) return;
        const label = checkbox.closest('label');
        checkbox.addEventListener('change', function () {
            label.classList.toggle('border-emerald-400', this.checked);
            label.classList.toggle('bg-emerald-50',      this.checked);
            label.classList.toggle('border-rose-200',    !this.checked);
        });
    });


    // ── 3. Cover Image Preview ───────────────────────────────────────────
    const coverInput    = document.getElementById('cover-image');
    const coverPreview  = document.getElementById('cover-image-preview');
    const coverPlaceholder = document.getElementById('cover-image-placeholder');
    const coverName     = document.getElementById('cover-image-name');

    coverInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const MAX_BYTES = 2 * 1024 * 1024; // 2 MB
        if (file.size > MAX_BYTES) {
            alert('ইমেজের আকার সর্বোচ্চ ২ MB হতে পারবে।');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            coverPreview.src = e.target.result;
            coverPreview.classList.remove('hidden');
            coverPlaceholder.classList.add('hidden');
            coverName.textContent = `✅ নির্বাচিত: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            coverName.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });


    // ── 4. Quill.js Initialization ───────────────────────────────────────
    // Wait for Quill to be available (loaded via defer)
    function initQuill() {
        if (typeof Quill === 'undefined') {
            setTimeout(initQuill, 100);
            return;
        }

        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: '#quill-toolbar',
            },
            placeholder: 'এখানে আপনার পোস্টের মূল বিষয়বস্তু লিখুন…',
        });

        // Pre-fill if there is old() content (validation failure repopulation)
        const hiddenInput = document.getElementById('body-raw-input');
        if (hiddenInput.value && hiddenInput.value.trim() !== '') {
            quill.clipboard.dangerouslyPasteHTML(hiddenInput.value);
        }

        // Sync Quill HTML → hidden input on every text change
        quill.on('text-change', function () {
            hiddenInput.value = quill.root.innerHTML;
        });

        // Also sync on form submit (safety net)
        document.getElementById('blog-create-form').addEventListener('submit', function (e) {
            hiddenInput.value = quill.root.innerHTML;

            // Client-side guard: body must not be empty
            if (quill.getText().trim().length < 10) {
                e.preventDefault();
                alert('পোস্টের বিষয়বস্তু অবশ্যই ন্যূনতম ১০ অক্ষরের হতে হবে।');
                quill.focus();
            }
        });
    }

    initQuill();

});
</script>
@endpush
