@extends('layouts.app')

@section('title', 'সিস্টেম অ্যাডমিন ড্যাশবোর্ড — রক্তদূত')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    
    {{-- 🎯 হেডার --}}
    <div class="mb-8 border-b border-slate-200 pb-5">
        <h1 class="text-3xl font-extrabold text-slate-900">অ্যাডমিন ড্যাশবোর্ড</h1>
        <p class="text-slate-500 font-medium mt-2">পুরো সিস্টেমের রিয়েল-টাইম ডেটা অ্যানালিটিক্স এবং পেন্ডিং ভেরিফিকেশন ম্যানেজ করুন।</p>
    </div>

    {{-- সাকসেস/এরর মেসেজ --}}
    @if(session('success'))
        <div class="mb-8 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-bold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- 📊 ১. গ্লোবাল স্ট্যাটিস্টিকস --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <x-card>
            <div class="text-slate-500 text-sm font-bold uppercase tracking-wider">মোট ইউজার</div>
            <div class="mt-2 text-4xl font-black text-slate-900">{{ $totalUsers }}</div>
        </x-card>
        <x-card>
            <div class="text-blue-600 text-sm font-bold uppercase tracking-wider">মোট ডোনার</div>
            <div class="mt-2 text-4xl font-black text-blue-600">{{ $totalDonors }}</div>
        </x-card>
        <x-card>
            <div class="text-red-600 text-sm font-bold uppercase tracking-wider">সফল রিকোয়েস্ট</div>
            <div class="mt-2 text-4xl font-black text-red-600">{{ $fulfilledRequests }} / {{ $totalRequests }}</div>
        </x-card>
        <x-card>
            <div class="text-emerald-600 text-sm font-bold uppercase tracking-wider">সাকসেস রেট</div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-600">{{ $successRate }}</span>
                <span class="text-emerald-400 font-bold text-sm">%</span>
            </div>
        </x-card>
    </div>

    {{-- 🪪 ২. NID ভেরিফিকেশন রিভিউ (Primary Queue) --}}
    <div x-data="{ openNidReview: false }" class="mb-12">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-blue-200 ring-2 ring-blue-50': openNidReview}">
            <button @click="openNidReview = !openNidReview"
                    @keydown.enter="openNidReview = !openNidReview"
                    @keydown.space.prevent="openNidReview = !openNidReview"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-blue-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl shrink-0 transition-transform"
                         :class="{'scale-110': openNidReview}">🪪</div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">NID ভেরিফিকেশন রিভিউ (অর্গানাইজেশন-বিহীন ইউজার)</h3>
                        <p class="text-sm text-slate-500 font-medium">অ্যাডমিন কিউ থেকে NID approve/reject করুন</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-md border border-blue-100">🪪 NID রিভিউ</span>
                    </div>
                    <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        {{ $pendingNids }} টি পেন্ডিং
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300"
                         :class="{'rotate-180 bg-blue-100 text-blue-600': openNidReview}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="openNidReview"
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        এখানে শুধুমাত্র অর্গানাইজেশন-বিহীন ইউজারদের NID রিভিউ হয়। অর্গানাইজেশন সদস্যদের NID তাদের org-admin যাচাই করবেন।
                    </p>
                    <a href="{{ route('admin.nid.reviews') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        প্যানেলে প্রবেশ করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 🏢 ৩. অর্গানাইজেশন/হাসপাতাল যাচাই --}}
    <div x-data="{ openOrgReview: false }" class="mb-12">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-indigo-200 ring-2 ring-indigo-50': openOrgReview}">
            <button @click="openOrgReview = !openOrgReview"
                    @keydown.enter="openOrgReview = !openOrgReview"
                    @keydown.space.prevent="openOrgReview = !openOrgReview"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-indigo-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl shrink-0 transition-transform"
                         :class="{'scale-110': openOrgReview}">🏥</div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">অর্গানাইজেশন/হাসপাতাল যাচাই</h3>
                        <p class="text-sm text-slate-500 font-medium">অফিশিয়াল ডকুমেন্ট যাচাই করে organization approve/reject করুন</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md border border-indigo-100">🏢 অর্গ ভেরিফাই</span>
                        <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2.5 py-1 rounded-md border border-emerald-100">✅ অ্যাপ্রুভ/রিজেক্ট</span>
                    </div>
                    <span class="bg-indigo-600 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        {{ $pendingOrgs }} টি পেন্ডিং
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300"
                         :class="{'rotate-180 bg-indigo-100 text-indigo-600': openOrgReview}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="openOrgReview"
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        হাসপাতাল/অর্গানাইজেশনের আবেদন ও অফিসিয়াল ডকুমেন্ট যাচাইয়ের জন্য dedicated review panel ব্যবহার করুন।
                    </p>
                    <a href="{{ route('admin.org.reviews') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        প্যানেলে প্রবেশ করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 🛡️ ৪. পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ - Secondary Queue) --}}
    <div x-data="{ openProofReview: false }" class="mb-12">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-red-200 ring-2 ring-red-50': openProofReview}">
            <button @click="openProofReview = !openProofReview"
                    @keydown.enter="openProofReview = !openProofReview"
                    @keydown.space.prevent="openProofReview = !openProofReview"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-red-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-2xl shrink-0 transition-transform"
                         :class="{'scale-110': openProofReview}">🛡️</div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">পেন্ডিং ভেরিফিকেশন (প্রুফ রিভিউ)</h3>
                        <p class="text-sm text-slate-500 font-medium">ডোনেশন প্রুফ queue review করে approve/reject করুন</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-md border border-amber-100">🟡 ক্লেইমড</span>
                        <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-red-100">🔴 ডিসপিউটেড</span>
                    </div>
                    <span class="bg-slate-800 text-white text-xs font-bold px-3 py-1.5 rounded-full">
                        {{ $pendingClaims }} টি পেন্ডিং
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300"
                         :class="{'rotate-180 bg-red-100 text-red-600': openProofReview}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>

            <div x-show="openProofReview"
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        বড় স্কেলে (যেমন ১০০+ কেস) ড্যাশবোর্ড পরিষ্কার রাখতে full proof review queue আলাদা প্যানেলে রাখা হয়েছে।
                    </p>
                    <a href="{{ route('admin.donations.proof_reviews') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        প্যানেলে প্রবেশ করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ⚙️ ৫. Governance & Moderation Tools (Expandable Accordion) --}}
    <div x-data="{ activeAccordion: null }" class="space-y-4 mb-8">
        <div class="flex items-center gap-2 mb-4 mt-8">
            <h2 class="text-xl font-extrabold text-slate-900">⚙️ গভার্নেন্স ও মডারেশন প্যানেল</h2>
        </div>

        {{-- Gamification Governance --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-red-200 ring-2 ring-red-50': activeAccordion === 1}">
            <button @click="activeAccordion = activeAccordion === 1 ? null : 1" 
                    @keydown.enter="activeAccordion = activeAccordion === 1 ? null : 1"
                    @keydown.space.prevent="activeAccordion = activeAccordion === 1 ? null : 1"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-red-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-2xl shrink-0 transition-transform" :class="{'scale-110': activeAccordion === 1}">🎮</div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Gamification Governance Panel</h3>
                        <p class="text-sm text-slate-500 font-medium">ডোনার পয়েন্ট অ্যাডজাস্ট, শ্যাডোব্যান এবং অ্যাক্টিভিটি অডিট</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-red-100">🚫 Shadowban</span>
                        <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-blue-100">🔧 Point Adjust</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="{'rotate-180 bg-red-100 text-red-600': activeAccordion === 1}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="activeAccordion === 1" 
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        ডোনারদের অর্জিত পয়েন্ট ম্যানুয়ালি রিভিউ ও মডিফাই করার জন্য এই প্যানেল ব্যবহার করুন। কোনো অস্বাভাবিক পয়েন্ট গেইন দেখা গেলে অ্যাকাউন্ট শ্যাডোব্যান বা রিস্টোর করতে পারবেন।
                    </p>
                    <a href="{{ route('admin.gamification.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        প্যানেলে প্রবেশ করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Blog Governance --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-violet-200 ring-2 ring-violet-50': activeAccordion === 2}">
            <button @click="activeAccordion = activeAccordion === 2 ? null : 2" 
                    @keydown.enter="activeAccordion = activeAccordion === 2 ? null : 2"
                    @keydown.space.prevent="activeAccordion = activeAccordion === 2 ? null : 2"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-violet-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-2xl shrink-0 transition-transform" :class="{'scale-110': activeAccordion === 2}">📝</div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <h3 class="text-lg font-extrabold text-slate-900">ব্লগ ও কন্টেন্ট মডারেশন</h3>
                            @if(isset($pendingBlogCount) && $pendingBlogCount > 0)
                                <span class="bg-red-100 text-red-600 text-[10px] font-black px-2 py-0.5 rounded-full border border-red-200 animate-pulse">
                                    🔴 {{ $pendingBlogCount }} পেন্ডিং
                                </span>
                            @else
                                <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-100">
                                    ✅ সব ক্লিয়ার
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 font-medium">ইউজারদের সাবমিট করা ব্লগ পোস্ট রিভিউ করুন</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-violet-50 text-violet-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-violet-100">✍️ রিভিউ</span>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-emerald-100">✅ অ্যাপ্রুভ</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="{'rotate-180 bg-violet-100 text-violet-600': activeAccordion === 2}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="activeAccordion === 2" 
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        পুরো সিস্টেমের কন্টেন্ট কোয়ালিটি কন্ট্রোল করুন। অপ্রাসঙ্গিক বা ভুয়া খবর ফিল্টার আউট করে শুধু মানসম্মত ব্লগ অ্যাপ্রুভ করুন।
                    </p>
                    <a href="{{ route('admin.blog.moderation.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        মডারেশন শুরু করুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Support Inbox Governance --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-blue-200 ring-2 ring-blue-50': activeAccordion === 4}">
            <button @click="activeAccordion = activeAccordion === 4 ? null : 4" 
                    @keydown.enter="activeAccordion = activeAccordion === 4 ? null : 4"
                    @keydown.space.prevent="activeAccordion = activeAccordion === 4 ? null : 4"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-blue-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-2xl shrink-0 transition-transform" :class="{'scale-110': activeAccordion === 4}">📬</div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-0.5">
                            <h3 class="text-lg font-extrabold text-slate-900">সাপোর্ট ইনবক্স</h3>
                            @if(isset($pendingSupportMessages) && $pendingSupportMessages > 0)
                                <span class="bg-red-100 text-red-600 text-[10px] font-black px-2 py-0.5 rounded-full border border-red-200 animate-pulse">
                                    🔴 {{ $pendingSupportMessages }} নতুন
                                </span>
                            @else
                                <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-100">
                                    ✅ সব ক্লিয়ার
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 font-medium">ইউজারদের পাঠানো সমস্যা এবং জিজ্ঞাসার উত্তর দিন</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-blue-100">💬 রিপ্লাই</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="{'rotate-180 bg-blue-100 text-blue-600': activeAccordion === 4}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="activeAccordion === 4" 
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        আমাদের প্ল্যাটফর্ম ব্যবহারকারীদের ফিডব্যাক এবং সমস্যাগুলি নিরীক্ষণ করুন। দ্রুত উত্তর প্রদানের মাধ্যমে ইউজারদের সুন্দর একটি অভিজ্ঞতা উপহার দিন।
                    </p>
                    <a href="{{ route('admin.support.messages.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        ইনবক্স খুলুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Analytics Dashboard --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-200"
             :class="{'border-red-200 ring-2 ring-red-50': activeAccordion === 3}">
            <button @click="activeAccordion = activeAccordion === 3 ? null : 3"
                    @keydown.enter="activeAccordion = activeAccordion === 3 ? null : 3"
                    @keydown.space.prevent="activeAccordion = activeAccordion === 3 ? null : 3"
                    class="w-full flex items-center justify-between p-5 bg-white hover:bg-red-50/30 transition-colors focus:outline-none">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-2xl shrink-0 transition-transform" :class="{'scale-110': activeAccordion === 3}">📊</div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900">Analytics Dashboard & CSV Export</h3>
                        <p class="text-sm text-slate-500 font-medium">রিয়েল-টাইম চার্ট, ট্রেন্ড অ্যানালাইসিস এবং প্রাইভেসি-সেইফ রিপোর্ট ডাউনলোড</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex flex-wrap gap-2">
                        <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-red-100">📈 Chart</span>
                        <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-md border border-emerald-100">⬇ CSV</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 transition-transform duration-300" :class="{'rotate-180 bg-red-100 text-red-600': activeAccordion === 3}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </button>
            <div x-show="activeAccordion === 3"
                 x-collapse
                 style="display: none;">
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    <p class="text-sm text-slate-600 font-semibold mb-5 max-w-2xl">
                        ডোনার-রিসিপিয়েন্ট সংখ্যা, রক্তের গ্রুপ বণ্টন, মাসভিত্তিক সফল রিকোয়েস্ট ট্রেন্ড—সব এক জায়গায় দেখুন।
                        প্রয়োজন হলে Anonymized CSV রিপোর্ট ডাউনলোড করতে পারবেন।
                    </p>
                    <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold px-6 py-3 rounded-xl text-sm transition shadow-sm">
                        অ্যানালিটিক্স ড্যাশবোর্ড খুলুন
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔒 ৫. সিকিউরিটি ও অডিট প্যানেল --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
        {{-- Security Radar Widget --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[400px]">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">🚨</span>
                        সন্দেহজনক কার্যক্রম
                    </h3>
                    <p class="text-xs text-slate-500 font-bold mt-1">সিস্টেম সিকিউরিটি রাডার (MVP-Lite)</p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-2xl font-black {{ $todaysSecurityEventsCount > 0 ? 'text-red-600' : 'text-emerald-500' }}">
                        {{ $todaysSecurityEventsCount }}
                    </span>
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">আজকের ইভেন্ট</span>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @if($recentSecurityLogs->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-400 mb-3 text-2xl">🛡️</div>
                        <p class="text-sm font-bold text-slate-500">কোনো সন্দেহজনক কার্যক্রম পাওয়া যায়নি। সিস্টেম নিরাপদ।</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentSecurityLogs as $log)
                            <li class="p-4 hover:bg-slate-50 transition flex gap-3">
                                <div class="w-2 h-2 mt-1.5 rounded-full bg-red-500 shrink-0 animate-pulse"></div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-100">{{ str_replace('_', ' ', $log->event_type) }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">{{ $log->description }}</p>
                                    @if($log->user)
                                        <p class="text-xs font-semibold text-slate-500 mt-1">ইউজার: <span class="text-slate-700">{{ $log->user->name }}</span> (ID: {{ $log->user->id }}) @if($log->ip_address) • IP: {{ $log->ip_address }} @endif</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Admin Audit Trail Widget --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-[400px]">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">📑</span>
                        অ্যাডমিন অডিট ট্রেইল
                    </h3>
                    <p class="text-xs text-slate-500 font-bold mt-1">সর্বশেষ ২০টি অ্যাকশন</p>
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto">
                @if($recentAuditLogs->isEmpty())
                    <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3 text-2xl">📋</div>
                        <p class="text-sm font-bold text-slate-500">এখনো কোনো অডিট লগ তৈরি হয়নি।</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentAuditLogs as $audit)
                            <li class="p-4 hover:bg-slate-50 transition flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-indigo-600 flex items-center justify-center font-black text-xs shrink-0 border border-slate-200">
                                    {{ mb_substr($audit->admin->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-0.5">
                                        <p class="text-sm font-extrabold text-slate-900 truncate pr-2">{{ $audit->admin->name ?? 'System Admin' }}</p>
                                        <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap shrink-0">{{ $audit->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-500 mb-1.5 flex items-center gap-1.5 flex-wrap">
                                        <span class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-100 uppercase tracking-wider text-[9px]">{{ str_replace('_', ' ', $audit->action_type) }}</span>
                                        @if($audit->target_id)
                                            <span>টার্গেট আইডি: <strong class="text-slate-700">{{ $audit->target_id }}</strong></span>
                                        @endif
                                    </div>
                                    @if(isset($audit->details['reason']))
                                        <p class="text-xs font-medium text-red-600 bg-red-50 p-2 rounded border border-red-100 mt-1">কারণ: {{ $audit->details['reason'] }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- 📈 ৬. চার্ট সেকশন (Professional Horizontal Bars) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">

        {{-- Pie Chart: ব্লাড গ্রুপ ডিমান্ড --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-7">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-sm">🩸</span>
                    ব্লাড গ্রুপ ডিমান্ড
                </h3>
                <p class="text-xs text-slate-500 font-semibold mt-1.5">কোন রক্তের গ্রুপ সবচেয়ে বেশি রিকোয়েস্ট হয়েছে</p>
            </div>
            
            @if(empty($bloodGroupDemand))
                <div class="flex flex-col items-center justify-center h-[240px] text-slate-400">
                    <span class="text-4xl mb-3">📊</span>
                    <span class="text-sm font-semibold">গত ৩০ দিনে কোনো ডিমান্ড নেই</span>
                </div>
            @else
                <div style="height:260px;">
                    <canvas id="bloodGroupChart"></canvas>
                </div>
            @endif
        </div>

        {{-- Bar Chart: জেলা ভিত্তিক ইমার্জেন্সি --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-7">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">📍</span>
                    শীর্ষ ৫ ইমার্জেন্সি জেলা (গত ৩০ দিন)
                </h3>
            </div>
            
            @if(empty($districtDemand))
                <div class="flex flex-col items-center justify-center h-[240px] text-slate-400">
                    <span class="text-4xl mb-3">📉</span>
                    <span class="text-sm font-semibold">গত ৩০ দিনে কোনো রিকোয়েস্ট নেই</span>
                </div>
            @else
                <div style="height:260px;">
                    <canvas id="districtChart"></canvas>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded
    if(typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Inter', 'Hind Siliguri', sans-serif";
    Chart.defaults.color = '#64748b';

    // ─── ১. ব্লাড গ্রুপ Pie Chart ────────────────────────────────
    @if(!empty($bloodGroupDemand))
    const bloodGroupData = @json($bloodGroupDemand);
    const bgColors = ['#ef4444','#f97316','#eab308','#22c55e','#06b6d4','#3b82f6','#8b5cf6','#ec4899'];
    new Chart(document.getElementById('bloodGroupChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(bloodGroupData),
            datasets: [{
                data: Object.values(bloodGroupData),
                backgroundColor: bgColors.slice(0, Object.keys(bloodGroupData).length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right', 
                    labels: { font: { size: 12, weight: 'bold', family: "'Inter', sans-serif" }, padding: 15, usePointStyle: true, pointStyle: 'circle' } 
                },
                tooltip: { 
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} রিকোয়েস্ট` } 
                }
            },
            cutout: '65%'
        }
    });
    @endif

    // ─── ২. জেলা Bar Chart ───────────────────────────────────────
    @if(!empty($districtDemand))
    const districtData = @json($districtDemand);
    new Chart(document.getElementById('districtChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(districtData),
            datasets: [{
                label: 'রিকোয়েস্ট সংখ্যা',
                data: Object.values(districtData),
                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                borderRadius: 4,
                barThickness: 20,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: { 
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ` ${ctx.label} — ${ctx.parsed.x} টি জরুরি রিকোয়েস্ট` } 
                } 
            },
            scales: {
                x: { 
                    beginAtZero: true, 
                    ticks: { precision: 0, font: { weight: '600' } }, 
                    grid: { color: '#f1f5f9', drawBorder: false } 
                },
                y: { 
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 12 }, color: '#1e293b' }
                }
            }
        }
    });
    @endif
});
</script>
@endsection
