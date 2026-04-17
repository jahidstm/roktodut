@extends('layouts.app')

@section('title', 'ব্লগ — স্বাস্থ্য তথ্য ও সাফল্যের গল্প | রক্তদূত')

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════════
     BLOG INDEX — রক্তদূত Blog Module
     Layout: full-width hero banner + 3-col main content + 1-col sidebar
     ═══════════════════════════════════════════════════════════════════════ --}}

{{-- ── Hero Banner ─────────────────────────────────────────────────────── --}}
<x-page-header variant="marketing" label="🩸 রক্তদূত ব্লগ" title="স্বাস্থ্য তথ্য ও <span class='text-yellow-300'>সাফল্যের গল্প</span>" subtitle="রক্তদানের অভিজ্ঞতা, চিকিৎসা পরামর্শ এবং আমাদের ডোনারদের অনুপ্রেরণামূলক গল্প পড়ুন।">
    <x-slot name="actions">
        <div class="flex flex-col items-center w-full">
            @auth
            <div class="mb-6">
                <x-secondary-button href="{{ route('blog.create') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    নতুন পোস্ট লিখুন
                </x-secondary-button>
            </div>
            @endauth

            {{-- Category Filter Pills --}}
            <div class="flex flex-wrap justify-center gap-3 w-full" id="blog-filter-pills">
                <a href="{{ route('blog.index') }}"
                   class="filter-pill {{ !request('type') ? 'active-pill' : '' }} inline-flex items-center gap-1.5 px-5 py-2 rounded-full font-bold text-sm transition-all duration-200
                          {{ !request('type') ? 'bg-white text-red-600 shadow-lg' : 'bg-white/15 text-white hover:bg-white/25 border border-white/20' }}">
                    📋 সব পোস্ট
                </a>
                <a href="{{ route('blog.index', ['type' => 'health']) }}"
                   class="filter-pill {{ request('type') === 'health' ? 'active-pill' : '' }} inline-flex items-center gap-1.5 px-5 py-2 rounded-full font-bold text-sm transition-all duration-200
                          {{ request('type') === 'health' ? 'bg-white text-red-600 shadow-lg' : 'bg-white/15 text-white hover:bg-white/25 border border-white/20' }}">
                    🏥 স্বাস্থ্য ব্লগ
                </a>
                <a href="{{ route('blog.index', ['type' => 'story']) }}"
                   class="filter-pill {{ request('type') === 'story' ? 'active-pill' : '' }} inline-flex items-center gap-1.5 px-5 py-2 rounded-full font-bold text-sm transition-all duration-200
                          {{ request('type') === 'story' ? 'bg-white text-red-600 shadow-lg' : 'bg-white/15 text-white hover:bg-white/25 border border-white/20' }}">
                    💪 সাফল্যের গল্প
                </a>
            </div>
        </div>
    </x-slot>
</x-page-header>

{{-- ── Main Content Area ────────────────────────────────────────────────── --}}
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 lg:py-14">
    <div class="flex flex-col lg:flex-row gap-8 xl:gap-10 items-start">

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  LEFT: Post Grid                                             ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <main class="flex-1 min-w-0" id="blog-post-grid" aria-label="ব্লগ পোস্ট তালিকা">

            {{-- ── Section Header ─── --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">
                        @if(request('type') === 'health')
                            🏥 স্বাস্থ্য ব্লগ
                        @elseif(request('type') === 'story')
                            💪 সাফল্যের গল্প
                        @else
                            সব ব্লগ পোস্ট
                        @endif
                    </h2>
                    @if(isset($posts) && $posts->total() > 0)
                        <p class="text-sm text-slate-500 font-semibold mt-0.5">
                            মোট {{ $posts->total() }}টি পোস্ট
                        </p>
                    @endif
                </div>
                {{-- Sort / results count chip --}}
                <span class="hidden sm:inline-flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                    </svg>
                    সাম্প্রতিক প্রথমে
                </span>
            </div>

            {{-- ── Post Grid ─── --}}
            @if(isset($posts) && $posts->count() > 0)

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6" id="posts-container">
                    @foreach($posts as $post)
                        @php
                            $isStory   = $post->type === 'story';
                            $isHealth  = $post->type === 'health';
                            $wordCount = str_word_count(strip_tags($post->body_sanitized ?? ''));
                            $readMins  = max(1, (int) ceil($wordCount / 200));

                            $displayName = '';
                            $showRealAvatar = true;
                            if ($isStory && $post->storyMeta) {
                                $lvl = $post->storyMeta->anonymize_level;
                                if ($lvl === 'anonymous') {
                                    $displayName = 'একজন রক্তদাতা';
                                    $showRealAvatar = false;
                                } elseif ($lvl === 'initials') {
                                    $parts = explode(' ', $post->author->name ?? '');
                                    $displayName = collect($parts)->map(fn($p) => mb_substr($p, 0, 1) . '.')->implode(' ');
                                    $showRealAvatar = false;
                                } else {
                                    $displayName = $post->author->name ?? 'অজানা';
                                }
                            } else {
                                $displayName = $post->author->name ?? 'অজানা';
                            }
                        @endphp

                        <article
                            id="post-card-{{ $post->id }}"
                            class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col hover:-translate-y-0.5">

                            {{-- Cover Image --}}
                            <a href="{{ route('blog.show', $post->slug) }}" class="block relative overflow-hidden shrink-0 aspect-video bg-gradient-to-br
                                {{ $isStory ? 'from-rose-100 via-red-50 to-pink-100' : 'from-blue-50 via-sky-50 to-teal-50' }}">

                                @if($post->cover_image)
                                    <img
                                        src="{{ asset('storage/' . $post->cover_image) }}"
                                        alt="{{ $post->title }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    {{-- Fallback illustrated placeholder --}}
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="text-5xl opacity-30">{{ $isStory ? '💪' : '🩺' }}</span>
                                        <span class="mt-2 text-xs font-bold uppercase tracking-widest text-slate-400 opacity-50">
                                            {{ $isStory ? 'Success Story' : 'Health Blog' }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Category Badge overlay --}}
                                <div class="absolute top-3 left-3">
                                    @if($isStory)
                                        <span class="inline-flex items-center gap-1 bg-rose-600 text-white text-[11px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-lg shadow-md">
                                            💪 সাফল্যের গল্প
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-sky-600 text-white text-[11px] font-extrabold uppercase tracking-wide px-2.5 py-1 rounded-lg shadow-md">
                                            🏥 স্বাস্থ্য ব্লগ
                                        </span>
                                    @endif
                                </div>

                                {{-- Verified Story badge --}}
                                @if($isStory && $post->storyMeta?->is_verified_story)
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center gap-1 bg-emerald-500 text-white text-[10px] font-extrabold px-2 py-1 rounded-lg shadow-md">
                                            ✅ Verified
                                        </span>
                                    </div>
                                @endif
                            </a>

                            {{-- Card Body --}}
                            <div class="flex flex-col flex-1 p-5">

                                {{-- Meta Row: read time + date --}}
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500">
                                        <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $readMins }} মিনিট পাঠযোগ্য
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs font-semibold text-slate-400">
                                        {{ $post->published_at?->locale('bn')->isoFormat('D MMM, YYYY') ?? $post->created_at->locale('bn')->isoFormat('D MMM, YYYY') }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <h3 class="font-extrabold text-slate-900 text-base leading-snug mb-2 group-hover:text-red-600 transition-colors duration-200 line-clamp-2">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>

                                {{-- Excerpt --}}
                                @if($post->excerpt)
                                    <p class="text-slate-500 text-sm leading-relaxed font-medium line-clamp-3 flex-1">
                                        {{ $post->excerpt }}
                                    </p>
                                @else
                                    <p class="text-slate-400 text-sm leading-relaxed line-clamp-3 flex-1">
                                        {{ Str::limit(strip_tags($post->body_sanitized ?? ''), 120) }}
                                    </p>
                                @endif

                                {{-- Categories --}}
                                @if($post->categories->count() > 0)
                                    <div class="flex flex-wrap gap-1.5 mt-3">
                                        @foreach($post->categories->take(3) as $cat)
                                            <span class="text-[11px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                                                {{ $cat->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Divider --}}
                                <div class="border-t border-slate-100 mt-4 pt-4">
                                    {{-- Author Row --}}
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2 min-w-0">
                                            {{-- Author Avatar --}}
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center shrink-0 text-white text-xs font-black overflow-hidden">
                                                @if(!$showRealAvatar)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                @elseif($post->author?->profile_image)
                                                    <img src="{{ asset('storage/' . $post->author->profile_image) }}" alt="Author" class="w-full h-full object-cover">
                                                @else
                                                    {{ mb_substr($displayName, 0, 1) }}
                                                @endif
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 truncate">{{ $displayName }}</span>
                                        </div>

                                        {{-- Read More CTA --}}
                                        <a href="{{ route('blog.show', $post->slug) }}"
                                           class="shrink-0 inline-flex items-center gap-1 text-red-600 text-xs font-extrabold hover:text-red-700 hover:gap-2 transition-all duration-150">
                                            পড়ুন
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                            </div>{{-- /.card-body --}}
                        </article>

                    @endforeach
                </div>

                {{-- ── Pagination ─── --}}
                @if($posts->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $posts->appends(request()->query())->links() }}
                    </div>
                @endif

            @else
                {{-- ── Empty State ─── --}}
                <div class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm">
                    <div class="w-20 h-20 mx-auto bg-red-50 rounded-full flex items-center justify-center mb-5">
                        <span class="text-4xl">📰</span>
                    </div>
                    <h3 class="text-xl font-extrabold text-slate-800 mb-2">কোনো পোস্ট পাওয়া যায়নি</h3>
                    <p class="text-slate-500 font-medium text-sm max-w-xs mx-auto leading-relaxed">
                        এই বিভাগে এখনো কোনো প্রকাশিত পোস্ট নেই। শীঘ্রই আসছে!
                    </p>
                    <a href="{{ route('blog.index') }}" class="mt-6 inline-flex items-center gap-2 text-red-600 font-extrabold text-sm hover:underline">
                        ← সব পোস্ট দেখুন
                    </a>
                </div>
            @endif

        </main>{{-- /post-grid --}}

        {{-- ╔══════════════════════════════════════════════════════════════╗
             ║  RIGHT: Sidebar                                              ║
             ╚══════════════════════════════════════════════════════════════╝ --}}
        <aside class="w-full lg:w-80 xl:w-84 shrink-0 space-y-6" aria-label="সাইডবার">
            <x-blog-sidebar :popular="$popularPosts ?? collect()" />
        </aside>

    </div>
</div>

@endsection
