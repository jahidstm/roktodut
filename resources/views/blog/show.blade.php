@extends('layouts.app')

@section('title', $post->title . ' | রক্তদূত ব্লগ')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════
     BLOG SHOW — Single Post View
     Security: body rendered via body_sanitized (HTMLPurifier-cleaned).
     Privacy: storyMeta anonymize_level controls author display.
     ═══════════════════════════════════════════════════════════════════════ --}}

@php
    $isStory  = $post->type === 'story';
    $isHealth = $post->type === 'health';

    // ── Read Time ────────────────────────────────────────────────────────
    $wordCount = str_word_count(strip_tags($post->body_sanitized ?? ''));
    $readMins  = max(1, (int) ceil($wordCount / 200));

    // ── Author Anonymization Logic ───────────────────────────────────────
    // anonymize_level: 'public' → show full name
    //                  'initials' → show initials
    //                  'anonymous' → show anonymous
    $authorName     = $post->author->name ?? 'অজানা';
    $isAnonymized   = false;
    $showRealAvatar = true;

    if ($isStory && $post->storyMeta) {
        $lvl = $post->storyMeta->anonymize_level;
        if ($lvl === 'anonymous') {
            $authorName     = 'একজন রক্তদাতা';
            $isAnonymized   = true;
            $showRealAvatar = false;
        } elseif ($lvl === 'initials') {
            // Show initials, e.g. "M. A."
            $parts = explode(' ', $post->author->name ?? '');
            $authorName     = collect($parts)->map(fn($p) => mb_substr($p, 0, 1) . '.')->implode(' ');
            $isAnonymized   = true;
            $showRealAvatar = false;
        }
    }

    // ── Verified Story ───────────────────────────────────────────────────
    $isVerified = $isStory && ($post->storyMeta?->is_verified_story ?? false);

    // ── District (for story) ─────────────────────────────────────────────
    $district   = $isStory ? ($post->storyMeta?->district ?? null) : null;
@endphp



{{-- ── Breadcrumb ───────────────────────────────────────────────────────── --}}
<div class="bg-white border-b border-slate-100">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 py-3">
        <nav class="flex items-center gap-2 text-sm font-semibold text-slate-400" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-red-600 transition-colors">হোম</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('blog.index') }}" class="hover:text-red-600 transition-colors">ব্লগ</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-600 truncate max-w-xs">{{ Str::limit($post->title, 45) }}</span>
        </nav>
    </div>
</div>

{{-- ── Hero Section ─────────────────────────────────────────────────────── --}}
<section id="post-hero" class="relative overflow-hidden
    {{ $isStory ? 'bg-gradient-to-br from-rose-700 via-red-600 to-red-500'
                : 'bg-gradient-to-br from-sky-800 via-sky-700 to-teal-600' }}">

    {{-- Grid overlay --}}
    <div class="absolute inset-0 opacity-10"
         style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px),
                                  linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px);
                background-size: 24px 24px;">
    </div>
    {{-- Ambient blobs --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-72 h-72 bg-black/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Hero Cover Image (if available) --}}
    @if($post->cover_image)
        <div class="absolute inset-0">
            <img src="{{ asset('storage/' . $post->cover_image) }}"
                 alt="{{ $post->title }}"
                 class="w-full h-full object-cover opacity-25">
            <div class="absolute inset-0 bg-gradient-to-t
                {{ $isStory ? 'from-red-700 via-red-600/80' : 'from-sky-800 via-sky-700/80' }}
                to-transparent"></div>
        </div>
    @endif

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6 py-14 md:py-20">

        {{-- Category + Verified Badge row --}}
        <div class="flex flex-wrap items-center gap-3 mb-6">
            {{-- Category pill --}}
            @if($isStory)
                <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm border border-white/25 text-white text-xs font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full">
                    💪 সাফল্যের গল্প
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm border border-white/25 text-white text-xs font-extrabold uppercase tracking-widest px-3.5 py-1.5 rounded-full">
                    🏥 স্বাস্থ্য ব্লগ
                </span>
            @endif

            {{-- ✅ Verified RoktoDut Success Story Badge (conditional) --}}
            @if($isVerified)
                <span id="verified-story-badge"
                      class="inline-flex items-center gap-1.5 bg-emerald-500 border border-emerald-400 text-white text-xs font-extrabold px-3.5 py-1.5 rounded-full shadow-lg animate-pulse-once">
                    ✅ Verified RoktoDut Success Story
                </span>
            @endif

            {{-- Medical Review Badge (for health posts) --}}
            @if($isHealth && $post->healthMeta?->hasReviewer())
                <span class="inline-flex items-center gap-1.5 bg-teal-500/80 border border-teal-300/40 text-white text-xs font-extrabold px-3.5 py-1.5 rounded-full">
                    👨‍⚕️ চিকিৎসক অনুমোদিত
                </span>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-2xl sm:text-3xl md:text-4xl xl:text-5xl font-extrabold text-white leading-tight tracking-tight drop-shadow max-w-4xl">
            {{ $post->title }}
        </h1>

        {{-- Excerpt --}}
        @if($post->excerpt)
            <p class="mt-4 text-white/75 text-base sm:text-lg font-medium max-w-3xl leading-relaxed">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Meta row: author + time + story district --}}
        <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3">

            {{-- Author Chip --}}
            <div class="flex items-center gap-2.5">
                {{-- Avatar --}}
                <div class="w-9 h-9 rounded-full border-2 border-white/40 flex items-center justify-center overflow-hidden
                    {{ $isStory ? 'bg-rose-400/30' : 'bg-sky-400/30' }} text-white font-black text-sm shrink-0">
                    @if(!$showRealAvatar)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    @elseif($post->author?->profile_image)
                        <img src="{{ asset('storage/' . $post->author->profile_image) }}" alt="Author" class="w-full h-full object-cover">
                    @else
                        {{ mb_substr($authorName, 0, 1) }}
                    @endif
                </div>
                <div>
                    <p class="text-white font-extrabold text-sm leading-none">{{ $authorName }}</p>
                    @if($isAnonymized)
                        <p class="text-white/55 text-[10px] font-bold uppercase tracking-wide mt-0.5">পরিচয় সুরক্ষিত</p>
                    @else
                        <p class="text-white/55 text-[10px] font-bold uppercase tracking-wide mt-0.5">লেখক</p>
                    @endif
                </div>
            </div>

            <span class="text-white/30 hidden sm:block">|</span>

            {{-- Read Time --}}
            <div class="flex items-center gap-1.5 text-white/70 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ $readMins }} মিনিট পাঠযোগ্য
            </div>

            {{-- Published Date --}}
            <div class="flex items-center gap-1.5 text-white/70 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $post->published_at?->locale('bn')->isoFormat('D MMMM, YYYY') ?? $post->created_at->locale('bn')->isoFormat('D MMMM, YYYY') }}
            </div>

            {{-- District (stories only) --}}
            @if($district)
                <div class="flex items-center gap-1.5 text-white/70 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $district }}
                </div>
            @endif

        </div>
    </div>
</section>

{{-- ── Main Content Layout ──────────────────────────────────────────────── --}}
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 lg:py-14">
    <div class="flex flex-col lg:flex-row gap-10 xl:gap-12 items-start">

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  LEFT: Article Body                                          ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <article class="flex-1 min-w-0" id="post-article">

            {{-- Privacy Notice Banner (for anonymized stories) --}}
            @if($isAnonymized)
                <div class="mb-7 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3.5">
                    <span class="text-amber-500 text-xl shrink-0 mt-0.5">🔒</span>
                    <div>
                        <p class="text-sm font-extrabold text-amber-800">পরিচয় সুরক্ষিত</p>
                        <p class="text-xs text-amber-700 font-medium mt-0.5 leading-relaxed">
                            লেখকের অনুরোধ অনুযায়ী এই গল্পে ব্যক্তিগত পরিচয় সুরক্ষিত রাখা হয়েছে।
                            রক্তদূত প্ল্যাটফর্ম গোপনীয়তাকে সর্বোচ্চ গুরুত্ব দেয়।
                        </p>
                    </div>
                </div>
            @endif

            {{-- Medical Review Notice (health posts) --}}
            @if($isHealth && $post->healthMeta?->hasReviewer())
                <div class="mb-7 flex items-start gap-3 bg-teal-50 border border-teal-200 rounded-xl px-4 py-3.5">
                    <span class="text-teal-600 text-xl shrink-0 mt-0.5">👨‍⚕️</span>
                    <div>
                        <p class="text-sm font-extrabold text-teal-800">চিকিৎসকীয়ভাবে পর্যালোচিত</p>
                        <p class="text-xs text-teal-700 font-medium mt-0.5">
                            এই নিবন্ধটি একজন যোগ্য চিকিৎসক কর্তৃক পর্যালোচনা করা হয়েছে।
                            @if($post->healthMeta->reviewer)
                                পর্যালোচক: <strong>{{ $post->healthMeta->reviewer->name }}</strong>
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- ── Post Body ─────────────────────────────────────────────
                 CRITICAL SECURITY: Only body_sanitized is rendered (HTMLPurifier-cleaned).
                 CRITICAL STYLING:  Wrapped in Tailwind prose for typography styles.
                 ─────────────────────────────────────────────────────────── --}}
            <div id="post-body-content"
                 class="prose prose-slate prose-lg max-w-none bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-8 sm:px-8 sm:py-10">
                {!! $post->body_sanitized !!}
            </div>

            {{-- ── Categories & Tags ─────────────────────────────────────── --}}
            @if($post->categories->count() > 0)
                <div class="mt-8 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mr-1">বিভাগ:</span>
                    @foreach($post->categories as $cat)
                        <span class="inline-flex items-center gap-1 text-sm font-bold text-red-700 bg-red-50 border border-red-100 px-3 py-1 rounded-full hover:bg-red-100 transition-colors cursor-default">
                            {{ $cat->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- ── Sources (health posts) ────────────────────────────────── --}}
            @if($isHealth && $post->healthMeta?->hasSources())
                <div class="mt-8 bg-slate-50 border border-slate-200 rounded-xl px-5 py-5">
                    <h3 class="text-sm font-extrabold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        তথ্যসূত্র
                    </h3>
                    <ol class="space-y-2">
                        @foreach($post->healthMeta->sources_json as $i => $src)
                            <li class="flex items-start gap-2 text-sm text-slate-600 font-medium">
                                <span class="shrink-0 text-red-600 font-black">{{ $i + 1 }}.</span>
                                @if(isset($src['url']))
                                    <a href="{{ $src['url'] }}" target="_blank" rel="noopener noreferrer"
                                       class="text-red-600 hover:underline">{{ $src['title'] ?? $src['url'] }}</a>
                                @else
                                    <span>{{ $src['title'] ?? $src }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            {{-- ── Share Strip ──────────────────────────────────────────── --}}
            <div class="mt-8 flex flex-wrap items-center gap-3 pt-6 border-t border-slate-100">
                <span class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">শেয়ার করুন:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                   target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold px-4 py-2 rounded-lg transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Facebook
                </a>
                <button onclick="navigator.clipboard.writeText('{{ request()->url() }}').then(()=>this.textContent='✓ কপি হয়েছে!')"
                        class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-extrabold px-4 py-2 rounded-lg transition-colors">
                    🔗 লিংক কপি করুন
                </button>
            </div>

            {{-- ── Back Navigation ──────────────────────────────────────── --}}
            <div class="mt-8">
                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center gap-2 text-slate-600 hover:text-red-600 font-extrabold text-sm transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    সব ব্লগ পোস্টে ফিরে যান
                </a>
            </div>

        </article>

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  RIGHT: Sidebar                                              ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <aside class="w-full lg:w-80 xl:w-84 shrink-0 space-y-6 lg:sticky lg:top-24" aria-label="সাইডবার">
            <x-blog-sidebar :popular="$popularPosts ?? collect()" />
        </aside>

    </div>
</div>

@endsection
