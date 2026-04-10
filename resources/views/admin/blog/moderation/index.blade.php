@extends('layouts.app')

@section('title', 'ব্লগ মডারেশন কিউ | রক্তদূত অ্যাডমিন')

@section('content')

{{-- ── Admin Header ──────────────────────────────────────────────────────── --}}
<section class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 overflow-hidden">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image: linear-gradient(rgba(255,255,255,1) 1px,transparent 1px),
                                  linear-gradient(90deg,rgba(255,255,255,1) 1px,transparent 1px);
                background-size: 28px 28px;"></div>

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6 py-10 md:py-14">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 bg-amber-500/15 border border-amber-500/30 text-amber-400 text-xs font-extrabold uppercase tracking-widest px-3 py-1 rounded-full mb-3">
                    🛡️ অ্যাডমিন প্যানেল
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">ব্লগ মডারেশন কিউ</h1>
                <p class="mt-1.5 text-slate-400 text-sm font-medium">পর্যালোচনার অপেক্ষায় থাকা পোস্টগুলো অনুমোদন বা বাতিল করুন।</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-slate-400 hover:text-white text-sm font-semibold transition-colors duration-150 shrink-0">
                ← অ্যাডমিন ড্যাশবোর্ড
            </a>
        </div>

        {{-- Stats Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8">
            @foreach([
                ['label' => 'মোট পেন্ডিং', 'value' => $stats['total_pending'],  'color' => 'amber',   'icon' => '⏳'],
                ['label' => 'সাফল্যের গল্প', 'value' => $stats['total_stories'], 'color' => 'rose',    'icon' => '💪'],
                ['label' => 'স্বাস্থ্য ব্লগ', 'value' => $stats['total_health'],  'color' => 'sky',     'icon' => '🏥'],
                ['label' => 'মোট বাতিল',    'value' => $stats['total_rejected'],'color' => 'red',     'icon' => '❌'],
            ] as $stat)
            <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
                <div class="text-xl mb-1">{{ $stat['icon'] }}</div>
                <div class="text-2xl font-extrabold text-white">{{ $stat['value'] }}</div>
                <div class="text-xs text-slate-400 font-semibold mt-0.5">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Flash Messages ─────────────────────────────────────────────────────── --}}
<div class="mx-auto max-w-6xl px-4 sm:px-6 pt-6">
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-emerald-700 font-semibold text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 font-semibold text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif
</div>

{{-- ── Main Content ────────────────────────────────────────────────────────── --}}
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-6 pb-16">

    @if($pendingPosts->isEmpty())
        {{-- Empty State --}}
        <div class="text-center py-24 bg-white rounded-2xl border border-slate-100 shadow-sm">
            <div class="w-20 h-20 mx-auto bg-emerald-50 rounded-full flex items-center justify-center mb-5">
                <span class="text-4xl">🎉</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-800 mb-2">কিউ ফাঁকা!</h2>
            <p class="text-slate-500 font-medium text-sm max-w-xs mx-auto">পর্যালোচনার জন্য কোনো পেন্ডিং পোস্ট নেই।</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($pendingPosts as $post)
                @php
                    $isStory   = $post->type === 'story';
                    $meta      = $post->storyMeta;
                    $hasRef    = $isStory && $meta && filled($meta->donation_ref_id) && filled($meta->donation_ref_type);
                    $wordCount = str_word_count(strip_tags($post->body_sanitized ?? ''));
                    $readMins  = max(1, (int) ceil($wordCount / 200));
                @endphp

                {{-- ── Post Card ── --}}
                <article
                    id="moderation-post-{{ $post->id }}"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

                    {{-- Card Header --}}
                    <div class="flex items-center justify-between gap-4 px-6 pt-5 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Type Badge --}}
                            @if($isStory)
                                <span class="shrink-0 inline-flex items-center gap-1 bg-rose-100 text-rose-700 text-xs font-extrabold px-2.5 py-1 rounded-full">
                                    💪 সাফল্যের গল্প
                                </span>
                            @else
                                <span class="shrink-0 inline-flex items-center gap-1 bg-sky-100 text-sky-700 text-xs font-extrabold px-2.5 py-1 rounded-full">
                                    🏥 স্বাস্থ্য ব্লগ
                                </span>
                            @endif

                            {{-- Title --}}
                            <h2 class="font-extrabold text-slate-900 text-base leading-snug truncate">
                                {{ $post->title }}
                            </h2>
                        </div>

                        {{-- Status & Date --}}
                        <div class="shrink-0 text-right">
                            <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-extrabold px-2.5 py-1 rounded-full">
                                ⏳ পেন্ডিং রিভিউ
                            </span>
                            <p class="text-xs text-slate-400 font-medium mt-1">
                                {{ $post->created_at->locale('bn')->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="px-6 py-5 grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {{-- LEFT: Content Preview --}}
                        <div class="lg:col-span-2 space-y-4">

                            {{-- Cover Image Thumbnail --}}
                            @if($post->cover_image)
                                <img src="{{ asset('storage/' . $post->cover_image) }}"
                                     alt="কভার ইমেজ"
                                     class="w-full max-h-52 object-cover rounded-xl border border-slate-100">
                            @endif

                            {{-- Excerpt / Body Preview --}}
                            <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed line-clamp-6"
                                 id="post-preview-{{ $post->id }}">
                                {!! $post->body_sanitized !!}
                            </div>

                            {{-- Read stats --}}
                            <p class="text-xs text-slate-400 font-semibold">
                                📖 আনুমানিক {{ $readMins }} মিনিট পাঠ &nbsp;·&nbsp; {{ $wordCount }} শব্দ
                            </p>
                        </div>

                        {{-- RIGHT: Author & Meta Info --}}
                        <div class="space-y-4">

                            {{-- Author --}}
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">লেখক</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center text-white text-xs font-black shrink-0 overflow-hidden">
                                        @if($post->author?->profile_image)
                                            <img src="{{ asset('storage/' . $post->author->profile_image) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            {{ mb_substr($post->author?->name ?? '?', 0, 1) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate">{{ $post->author?->name ?? 'অজানা' }}</p>
                                        <p class="text-xs text-slate-400 font-medium truncate">{{ $post->author?->email }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Story Meta (if story type) --}}
                            @if($isStory && $meta)
                                <div class="bg-rose-50 rounded-xl p-4 border border-rose-100">
                                    <p class="text-xs font-extrabold text-rose-600 uppercase tracking-wider mb-3">গল্পের মেটাডেটা</p>

                                    <div class="space-y-2 text-xs font-semibold">
                                        {{-- Anonymization --}}
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-slate-500">পরিচয়:</span>
                                            <span class="text-rose-700 font-extrabold">
                                                @match($meta->anonymize_level)
                                                    'public'    => '👤 নিজ নামে',
                                                    'initials'  => '🔤 আদ্যক্ষর',
                                                    'anonymous' => '🎭 সম্পূর্ণ গোপন',
                                                    default     => $meta->anonymize_level
                                                @endmatch
                                            </span>
                                        </div>

                                        {{-- District --}}
                                        @if($meta->district)
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-slate-500">জেলা:</span>
                                            <span class="text-slate-700">{{ $meta->district }}</span>
                                        </div>
                                        @endif

                                        {{-- Reference --}}
                                        @if($hasRef)
                                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-rose-200">
                                            <span class="text-slate-500">রেফারেন্স:</span>
                                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-extrabold">
                                                ✅ {{ $meta->donation_ref_type === 'donation' ? 'Donation' : 'Blood Request' }} #{{ $meta->donation_ref_id }}
                                            </span>
                                        </div>
                                        @else
                                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-rose-200">
                                            <span class="text-slate-500">রেফারেন্স:</span>
                                            <span class="text-slate-400">নেই</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Categories --}}
                            @if($post->categories->count() > 0)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($post->categories as $cat)
                                        <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                                            {{ $cat->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                        </div>{{-- /right --}}
                    </div>{{-- /card-body --}}

                    {{-- ── Action Bar ── --}}
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-wrap items-center gap-3">

                        {{-- APPROVE --}}
                        <button
                            type="button"
                            data-post-id="{{ $post->id }}"
                            data-has-ref="{{ $hasRef ? '1' : '0' }}"
                            onclick="openApproveModal({{ $post->id }}, {{ $hasRef ? 'true' : 'false' }})"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600
                                   text-white font-extrabold text-sm shadow-sm hover:shadow-md
                                   transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            অনুমোদন করুন
                        </button>

                        {{-- REJECT --}}
                        <button
                            type="button"
                            onclick="openRejectModal({{ $post->id }}, '{{ addslashes($post->title) }}')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                                   bg-red-50 hover:bg-red-100 border border-red-200
                                   text-red-700 font-extrabold text-sm
                                   transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            বাতিল করুন
                        </button>

                        {{-- VIEW FULL (opens in new tab for full blog preview) --}}
                        <a href="{{ route('blog.show', $post->slug) }}"
                           target="_blank"
                           class="ml-auto inline-flex items-center gap-1.5 text-slate-600 hover:text-red-600 text-xs font-bold transition-colors duration-150">
                            পূর্ণ পোস্ট দেখুন
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                    </div>{{-- /action-bar --}}
                </article>

            @endforeach
        </div>{{-- /posts --}}

        {{-- Pagination --}}
        @if($pendingPosts->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $pendingPosts->links() }}
            </div>
        @endif
    @endif
</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     APPROVE MODAL
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="approve-modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="approve-modal-title"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-slate-100">
            <h3 id="approve-modal-title" class="text-lg font-extrabold text-slate-900">✅ পোস্ট অনুমোদন করুন</h3>
            <p class="text-sm text-slate-500 font-medium mt-1">পোস্টটি প্রকাশিত হবে এবং সবার কাছে দেখাবে।</p>
        </div>

        <form id="approve-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            {{-- Verified Story option (shown only if story has a valid ref) --}}
            <div id="verify-story-toggle" class="hidden">
                <label id="verify-story-label"
                       class="flex items-start gap-3 cursor-pointer p-4 rounded-xl border-2 border-slate-200 hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-200">
                    <input
                        type="checkbox"
                        id="verify-story-checkbox"
                        name="verify_story"
                        value="1"
                        class="mt-0.5 w-5 h-5 rounded accent-emerald-500 shrink-0 cursor-pointer">
                    <div>
                        <p class="font-extrabold text-slate-800 text-sm">✅ "যাচাইকৃত গল্প" হিসেবে চিহ্নিত করুন</p>
                        <p class="text-xs text-slate-500 font-medium mt-0.5 leading-snug">
                            একটি বৈধ ডোনেশন বা ব্লাড রিকোয়েস্ট রেফারেন্স পাওয়া গেছে। অনুমোদন করলে গল্পটিতে "Verified" ব্যাজ দেখাবে।
                        </p>
                    </div>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-extrabold text-sm shadow-sm transition-all duration-200">
                    হ্যাঁ, অনুমোদন করুন
                </button>
                <button type="button"
                        onclick="closeModal('approve-modal')"
                        class="flex-1 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all duration-200">
                    বাতিল
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════════════
     REJECT MODAL
     ══════════════════════════════════════════════════════════════════════════ --}}
<div id="reject-modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="reject-modal-title"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-slate-100">
            <h3 id="reject-modal-title" class="text-lg font-extrabold text-slate-900">❌ পোস্ট বাতিল করুন</h3>
            <p id="reject-modal-subtitle" class="text-sm text-slate-500 font-medium mt-1"></p>
        </div>

        <form id="reject-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="rejection-reason" class="block text-sm font-extrabold text-slate-700 mb-1.5">
                    বাতিলের কারণ
                    <span class="text-slate-400 font-normal text-xs">(ঐচ্ছিক — লেখককে জানানো হবে)</span>
                </label>
                <textarea
                    id="rejection-reason"
                    name="rejection_reason"
                    rows="3"
                    maxlength="500"
                    placeholder="যেমন: ব্যক্তিগত তথ্য প্রকাশ, বিষয়বস্তু নির্দেশিকা লঙ্ঘন…"
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium text-sm
                           placeholder:text-slate-400 resize-none
                           focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-all duration-200"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white font-extrabold text-sm shadow-sm transition-all duration-200">
                    হ্যাঁ, বাতিল করুন
                </button>
                <button type="button"
                        onclick="closeModal('reject-modal')"
                        class="flex-1 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all duration-200">
                    না, ফিরে যাই
                </button>
            </div>
        </form>
    </div>
</div>

@endsection


@push('scripts')
<script>
// ── Named route base URLs (resolved server-side) ─────────────────────────────
const APPROVE_BASE = '{{ rtrim(route("admin.blog.moderation.approve", ["post" => 0]), "0") }}';
const REJECT_BASE  = '{{ rtrim(route("admin.blog.moderation.reject",  ["post" => 0]), "0") }}';

// ── Modal Helpers ────────────────────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close on backdrop click
['approve-modal', 'reject-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function (e) {
        if (e.target === this) closeModal(id);
    });
});

// Close on Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeModal('approve-modal');
        closeModal('reject-modal');
    }
});

// ── Open Approve Modal ────────────────────────────────────────────────────────
function openApproveModal(postId, hasRef) {
    document.getElementById('approve-form').action = APPROVE_BASE + postId + '/approve';

    const toggle = document.getElementById('verify-story-toggle');
    if (hasRef) {
        toggle.classList.remove('hidden');
    } else {
        toggle.classList.add('hidden');
        document.getElementById('verify-story-checkbox').checked = false;
    }

    openModal('approve-modal');
}

// ── Open Reject Modal ─────────────────────────────────────────────────────────
function openRejectModal(postId, title) {
    document.getElementById('reject-form').action = REJECT_BASE + postId + '/reject';

    document.getElementById('reject-modal-subtitle').textContent =
        `"${title}" — এই পোস্টটি প্রকাশিত হবে না।`;

    document.getElementById('rejection-reason').value = '';

    openModal('reject-modal');
}
</script>
@endpush
